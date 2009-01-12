let dir_sep = match Sys.os_type with
	| "Unix" -> "/"
	| _ -> "\\"

let basename s =
	let debut = try String.rindex s '/' +1 with _ -> 0 in
	String.sub s debut (String.length s - debut)

let filecontent s =
	let inchan = open_in_bin s in
	let length = in_channel_length inchan in
	let string = String.create length in
	really_input inchan string 0 length;
	close_in_noerr inchan;
	string

let random_string =
	Random.self_init ();
	let len = Random.int 50 in
	let s = String.create len in
	for i = 0 to len-1 do
		s.[i] <- char_of_int (Random.int 80 + 40)
	done;
	s

let rec find_str s subs i =
	let l = String.length subs in
	if String.length s < l then raise Not_found else begin
		if String.sub s i l = subs then i else find_str s subs (i+1)
	end

module HTTP = struct
	let http_request sa req =
		let sock = Unix.socket Unix.PF_INET Unix.SOCK_STREAM 0 in
		Unix.connect sock sa;
		let to_send = String.length req in
		let sent = ref 0 in
		while !sent <> to_send do
			sent := !sent + Unix.send sock req !sent (to_send - !sent) []
		done;
		let s = " " and r = ref "" in
		while Unix.recv sock s 0 1 [] > 0 do
			r := !r ^ s
		done;
		Unix.close sock;
		!r
	
	let rec get_http_body_aux s i = match (s.[i], s.[i+1], s.[i+2], s.[i+3]) with
		| ('\r', '\n', '\r', '\n') -> String.sub s (i+4) (String.length s - i - 4)
		| (_, '\r', '\n', '\r') -> get_http_body_aux s (i+1)
		| (_, _, '\r', '\n') -> get_http_body_aux s (i+2)
		| (_, _, _, '\r') -> get_http_body_aux s (i+3)
		| _ -> get_http_body_aux s (i+4)
	
	let get_http_body s = get_http_body_aux s 0
	
	let rec get_header_aux h s i =
		if s.[i] == '\r' then None else
		try
			if String.sub s i (String.length h) = h then begin
				let fin = String.index_from s i '\r' in
				Some (String.sub s (i+(String.length h +1)) (fin-i-(String.length h +1)))
			end else
				get_header_aux h s ((String.index_from s i '\n') + 1)
		with _ -> None

	let get_cookie s = get_header_aux "Set-Cookie:" s 0
	let get_location s  = get_header_aux "Location:" s 0

	let format_data post_data =
		let to_string data = match data with
		| `Text (name, value) ->
			"Content-Disposition: form-data; name=\"" ^ name ^ "\"\r\n\r\n" ^ value
		| `Image (name, filename) ->
			"Content-Disposition: form-data; name=\"" ^ name ^ "\"; filename=\"" ^ basename filename ^ "\"\r\n"
			^ "Content-Type: image/jpeg\r\n"
			^ "Content-Transfer-Encoding: binary\r\n\r\n"
			^ filecontent filename
		in
		let boundary = random_string in
		boundary, List.fold_right (fun s acc -> "\r\n--"^ boundary ^ "\r\n" ^ s ^ acc) (List.map to_string post_data) ("\r\n--"^ boundary ^"--" )
	
	let post proxy url cookie post_data =
		let url_host, url_port = begin
			assert (String.sub url 0 7 = "http://");
			let slash = String.index_from url 7 '/' in
			let deuxpoints = try String.index_from url 7 ':' with _ -> slash in
			let host = String.sub url 7 (deuxpoints - 7) in
			let port = try int_of_string (String.sub url (deuxpoints+1) (slash-deuxpoints-1)) with _ -> 80 in
			host, port
			end
		in
		let sa = match proxy with
			| None -> Unix.ADDR_INET ((Unix.gethostbyname url_host).Unix.h_addr_list.(0), url_port)
			| Some (host, port) -> Unix.ADDR_INET ((Unix.gethostbyname host).Unix.h_addr_list.(0), port)
		in
		let cookie_line = match cookie with
			| None -> ""
			| Some s -> "Cookie: " ^ s ^ "\r\n"
		in
		let boundary, content = format_data post_data in
		let request = "POST " ^ url ^ " HTTP/1.0\r\n"
			^ "Host: " ^ url_host ^ "\r\n"
			^ cookie_line
			^ "Content-Type: multipart/form-data; boundary=" ^ boundary ^ "\r\n"
			^ "Content-Length: " ^ string_of_int (String.length content) ^ "\r\n\r\n"
			^ content
		in
		http_request sa request
end

let get_int s name =
	try
		let debut = find_str s name 0 + String.length name in
		let fin = try String.index_from s debut '&' with _ -> String.length s in
		Some (int_of_string (String.sub s debut (fin-debut)))
	with _ -> None

let config =
	object (self)
	val mutable proxy_host = None
	val mutable proxy_port = None
	val mutable url = None
	val mutable cookie = None
	val dir = match Sys.os_type with
		| "Unix" -> Sys.getenv "HOME" ^ "/.photoalbum"
		| _ -> Sys.getenv "APPDATA" ^ "\\Photoalbum"
	
	method proxy = match proxy_host, proxy_port with
		| None, _ -> None
		| Some h, None -> Some (h, 3128)
		| Some h, Some p -> Some (h, p)
	method url = match url with
		| None -> "http://www.kyklydse.com/photoalbum"
		| Some u -> u
	method cookie = cookie
	
	method set_cookie c = cookie <- c
	method set_url u = url <- u
	method set_proxy_host h = proxy_host <- h
	method set_proxy_port p = proxy_port <- p
	
	method load () =
		try
			let inch = open_in (dir ^ dir_sep ^ "config") in
			while true do
				let s = input_line inch in
				let l = String.length s in
				match () with
					| _ when l > 7 && String.sub s 0 7 = "Cookie=" -> cookie <- Some (String.sub s 7 (l-7))
					| _ when l > 4 && String.sub s 0 4 = "Url=" -> url <- Some (String.sub s 4 (l-4))
					| _ when l > 9 && String.sub s 0 9 = "ProxyHost" -> proxy_host <- Some (String.sub s 9 (l-9))
					| _ when l > 9 && String.sub s 0 9 = "ProxyPort" -> proxy_port <- Some (int_of_string (String.sub s 9 (l-9)))
					| _ -> ()
			done;
			close_in inch
		with _ -> ()
	
	method save () =
		if not (Sys.file_exists dir) then Unix.mkdir dir 0o700;
		let outch = open_out_gen [Open_wronly; Open_creat; Open_trunc] 0o700 (dir ^ dir_sep ^ "config") in
		begin match cookie with
			| Some c -> output_string outch ("Cookie=" ^ c ^ "\n")
			| None -> ()
		end;
		begin match url with
			| Some u -> output_string outch ("Url=" ^ u ^ "\n")
			| None -> ()
		end;
		begin match proxy_host with
			| Some h -> output_string outch ("ProxyHost=" ^ h ^ "\n")
			| None -> ()
		end;
		begin match proxy_port with
			| Some p -> output_string outch ("ProxyPort=" ^ string_of_int p ^ "\n")
			| None -> ()
		end;
		close_out outch

end

let rec find_element elt list = match list with
	| [] -> raise Not_found
	| a::s when Xml.tag a = elt -> a
	| _::s -> find_element elt s

module API = struct
	let xml_parser = XmlParser.make ()
	let _ = XmlParser.prove xml_parser false
	let parse_string s = XmlParser.parse xml_parser (XmlParser.SString s)
	
	let cookie = ref None
	
	let login email passwd =
		let post_data = [
			`Text ("action", "login");
			`Text ("email", email);
			`Text ("passwd", passwd) ]
		in
		let post_url = config#url ^ "/login.php" in
		let resp = HTTP.post config#proxy post_url None post_data in
		cookie := HTTP.get_cookie resp

	let logout () = cookie := None
	
	let listalbums () =
		let post_data = [`Text ("xml", "xml")] in
		let post_url = config#url ^ "/index.php" in
		let resp = HTTP.post config#proxy post_url !cookie post_data in
		let xml = parse_string (HTTP.get_http_body resp) in
		let children = Xml.children xml in
		let body = find_element "body" children in
		let albums = Xml.children body in
		let f xml =
			let children = Xml.children xml in
			let id = int_of_string (Xml.pcdata (List.hd (Xml.children (find_element "id" children)))) in
			let name = Xml.pcdata (List.hd (Xml.children (find_element "name" children))) in
			let author = Xml.pcdata (List.hd (Xml.children (find_element "author" children))) in
			let nbphotos = int_of_string (Xml.pcdata (List.hd (Xml.children (find_element "nbphotos" children)))) in
			let owner = List.mem (Xml.Element ("owner", [], [])) children in 
			id, name, author, nbphotos, owner
		in
		List.map f albums
	
	let newalbum title date =
		let post_data = [
			`Text ("action", "addalbum");
			`Text ("title", title);
			`Text ("album_date", date) ]
		in
		let post_url = config#url ^ "/newalbum.php" in
		let resp = HTTP.post config#proxy post_url !cookie post_data in
		match HTTP.get_location resp with
			| Some loc -> get_int loc "id_album="
			| None -> None
	
	let newphoto id_album filename legend =
		let post_data = [
			`Text ("action", "newphoto");
			`Text ("id_album", string_of_int id_album);
			`Text ("legend", legend);
			`Image ("photo", filename) ]
		in
		let post_url = config#url ^ "/newphoto.php" in
		ignore (HTTP.post config#proxy post_url !cookie post_data)
end


module UI = struct
	type photo_record = {mutable filename : string; mutable checked : bool; mutable legend : string}
	
	let window = GWindow.window ~title:"Photoalbum" ~width:500 ~height:400 ()
	let vbox = GPack.vbox ~packing:window#add ()
	let toolbar = GButton.toolbar ~packing:vbox#pack ()
	
	let config_button = GButton.tool_button ~label:"Préférences" ~stock:`PREFERENCES ~packing:toolbar#insert ()
	let open_button = GButton.tool_button ~label:"Ouvrir un dossier" ~stock:`OPEN ~packing:toolbar#insert ()
	let login_button = GButton.tool_button ~label:"Se connecter" ~stock:`CONNECT ~packing:toolbar#insert ()
	let logout_button = GButton.tool_button ~label:"Se déconnecter" ~stock:`DISCONNECT ~packing:toolbar#insert ()
	let upload_button = GButton.tool_button ~label:"Envoyer les photos" ~stock:`GO_FORWARD ~packing:toolbar#insert ()

	let photos_scroll = GBin.scrolled_window ~hpolicy:`NEVER ~packing:(vbox#pack ~expand:true) ()
	let photos_vbox = GPack.vbox ~packing:photos_scroll#add_with_viewport ~spacing:3 ~show:false ()
	let photos = ref [||]
	let set_photos l =
		List.iter photos_vbox#remove photos_vbox#all_children;
		photos := Array.of_list (List.map (fun f -> {filename=f; checked=true; legend=""}) l);
		let add_photo i ph =
			let hbox = GPack.hbox ~packing:photos_vbox#pack () in
			let checkbox = GButton.check_button ~active:true ~packing:hbox#pack () in
			let pixbuf = GdkPixbuf.from_file_at_size !photos.(i).filename ~width:200 ~height:200 in
			let image = GMisc.image ~pixbuf ~width:200 ~packing:hbox#pack () in
			let entry = GEdit.entry ~packing:(hbox#pack ~expand:true) () in
			ignore (checkbox#connect#toggled ~callback:(fun () -> !photos.(i).checked <- checkbox#active));
			ignore (entry#connect#changed ~callback:(fun () -> !photos.(i).legend <- entry#text))
		in
		Array.iteri add_photo !photos;
		photos_vbox#misc#show_all ()
			
	
	let edit_config () =
		let dialog = GWindow.dialog ~title:"Préférences" ~modal:true () in
		let url_hbox = GPack.hbox ~packing:dialog#vbox#add () in
		let url_label = GMisc.label ~text:"Url :" ~packing:url_hbox#pack () in
		let url_entry = GEdit.entry ~text:config#url ~packing:(url_hbox#pack ~expand:true) () in
		let proxy_hbox = GPack.hbox ~packing:dialog#vbox#pack () in
		let proxyhost_label = GMisc.label ~text:"Proxy :" ~packing:proxy_hbox#pack () in
		let proxyhost_entry = GEdit.entry ~text:(match config#proxy with Some (h, _) -> h | None -> "") ~packing:(proxy_hbox#pack ~expand:true) () in
		let proxyport_label = GMisc.label ~text:"Port :" ~packing:proxy_hbox#pack () in
		let proxyport_entry = GEdit.entry ~width_chars:5 ~text:(match config#proxy with Some (_, p) -> string_of_int p | None -> "") ~packing:proxy_hbox#pack () in
		dialog#add_button_stock `OK `OK;
		dialog#add_button_stock `CANCEL `CANCEL;
		begin match dialog#run () with
			| `OK -> begin
				if config#url <> url_entry#text then begin
					config#set_url (Some url_entry#text);
					API.cookie := None;
					config#set_cookie None;
					login_button#misc#set_sensitive true;
					logout_button#misc#set_sensitive false
				end;
				begin match proxyhost_entry#text with
					| "" -> config#set_proxy_host None
					| h -> config#set_proxy_host (Some h)
				end;
				begin match proxyport_entry#text with
					| "" ->  config#set_proxy_port None
					| p -> config#set_proxy_port (Some (int_of_string p))
				end;
				config#save ()
				end
			| _ -> ()
		end;
		dialog#destroy ()
	
	let open_dir () =
		let dialog = GWindow.file_chooser_dialog ~action:`SELECT_FOLDER ~modal:true () in
		dialog#add_button_stock `CANCEL `CANCEL;
		dialog#add_button_stock `OPEN `OPEN;
		match dialog#run () with
			| `OPEN -> begin
				let dirname = match dialog#current_folder with
					| None -> assert false
					| Some d -> d
				in
				dialog#destroy ();
				let files = Array.to_list (Sys.readdir dirname) in
				let is_jpg s = String.lowercase (String.sub s (String.length s - 4) 4) = ".jpg" in
				let photos = List.sort compare (List.filter is_jpg files) in
				set_photos (List.map (fun f -> dirname ^ dir_sep ^ f) photos);
				if !API.cookie <> None then upload_button#misc#set_sensitive true
				end
			| _ -> dialog#destroy ()

	let login () =
		let dialog = GWindow.dialog ~title:"Connexion" ~width:300 ~modal:true () in
		let email_hbox = GPack.hbox ~packing:dialog#vbox#add () in
		let email_label = GMisc.label ~text:"Email :" ~packing:email_hbox#pack () in
		let email_entry = GEdit.entry ~packing:(email_hbox#pack ~expand:true) () in
		let passwd_hbox = GPack.hbox ~packing:dialog#vbox#add () in
		let passwd_label = GMisc.label ~text:"Mot de passe :" ~packing:passwd_hbox#pack ()in
		let passwd_entry = GEdit.entry ~visibility:false ~packing:(passwd_hbox#pack ~expand:true) () in
		let memo = GButton.check_button ~label:"Mémoriser les informations de connexion" ~packing:dialog#vbox#pack () in
		dialog#add_button_stock `OK `OK;
		dialog#add_button_stock `CANCEL `CANCEL;
		match dialog#run () with
			| `OK -> begin
				API.login email_entry#text passwd_entry#text;
				if !API.cookie <> None then begin
					login_button#misc#set_sensitive false;
					logout_button#misc#set_sensitive true;
					if memo#active then begin
						config#set_cookie !API.cookie;
						config#save ()
					end;
					if Array.length !photos <> 0 then
						upload_button#misc#set_sensitive true
				end;
				dialog#destroy ()
				end
			| _ -> dialog#destroy ()
	
	let logout () =
		API.logout ();
		login_button#misc#set_sensitive true;
		logout_button#misc#set_sensitive false;
		upload_button#misc#set_sensitive false;
		config#set_cookie None;
		config#save ()
	
	let real_upload id_album list =
		let n = List.length list in
		let i = ref 0 and l = ref list in
		let dialog = GWindow.dialog ~title:"Envoi des photos" ~width:300 ~modal:true () in
		let label = GMisc.label ~text:("Envoi de " ^ string_of_int n ^ " photos") ~packing:dialog#vbox#pack () in
		let progress = GRange.progress_bar ~packing:dialog#vbox#pack () in
		progress#set_text ("0/" ^ string_of_int n ^ " photos");
		dialog#show ();
		let f () =
			while !l <> [] do
				let (filename, legend) = List.hd !l in
				API.newphoto id_album filename legend;
				i := !i + 1;
				l := List.tl !l;
				GtkThread.sync progress#set_fraction (float_of_int !i /. float_of_int n);
				GtkThread.sync progress#set_text (string_of_int !i ^ "/" ^ string_of_int n ^ " photos")
			done;
			GtkThread.sync dialog#destroy ()
		in
		ignore (Thread.create f ())
	
	let upload () =
		let dialog = GWindow.dialog ~title:"Choisir un album" ~modal:true () in
		let list = API.listalbums () in
		let list = List.filter (fun (_,_,_,_,b) -> b) list in
		let id_album = ref (-1) in
		if list <> [] then begin
			let id, name, author, nb_photo, owner = List.hd list in
			id_album := id;
			let radio_button = GButton.radio_button ~label:name ~packing:dialog#vbox#pack () in
			ignore (radio_button#connect#toggled ~callback:(fun () -> if radio_button#active then id_album := id));
			let group = radio_button#group in
			let f album =
				let id, name, author, nb_photo, owner = album in
				let radio_button = GButton.radio_button ~group ~label:name ~packing:dialog#vbox#pack () in
				ignore (radio_button#connect#toggled ~callback:(fun () -> if radio_button#active then id_album := id))
			in
			List.iter f (List.tl list)
		end;
		let photos_list = ref [] in
		for i = Array.length !photos - 1 downto 0 do
			if !photos.(i).checked then
				photos_list := (!photos.(i).filename, !photos.(i).legend) :: !photos_list
		done;
		if list <> [] then dialog#add_button_stock `OK `OK;
		dialog#add_button_stock `CANCEL `CANCEL;
		dialog#add_button_stock `NEW `NEW;
		match dialog#run () with
			| `OK -> begin
				if !id_album <> -1 then
					real_upload !id_album !photos_list;
				dialog#destroy ()
				end
			| `NEW -> begin
				dialog#destroy ();
				let dialog2 = GWindow.dialog ~title:"Nouvel album" ~modal:true () in
				let title_hbox = GPack.hbox ~packing:dialog2#vbox#pack () in
				let title_label = GMisc.label ~text:"Titre de l'album :" ~packing:title_hbox#pack () in
				let title_entry = GEdit.entry ~packing:(title_hbox#pack ~expand:true) () in
				let date_hbox = GPack.hbox ~packing:dialog2#vbox#pack () in
				let date_label = GMisc.label ~text:"Date de l'album :" ~packing:date_hbox#pack () in
				let today =
					let time = Unix.localtime (Unix.time ()) in
					string_of_int (time.Unix.tm_year+1900) ^ "-" ^ string_of_int (time.Unix.tm_mon +1) ^ "-" ^ string_of_int (time.Unix.tm_mday)
				in
				let date_entry = GEdit.entry ~text:today ~packing:(date_hbox#pack ~expand:true) () in
				dialog2#add_button_stock `OK `OK;
				dialog2#add_button_stock `CANCEL `CANCEL;
				begin match dialog2#run () with
					| `OK -> begin
						match API.newalbum title_entry#text date_entry#text with
							| None -> dialog2#destroy ()
							| Some id_album -> begin dialog2#destroy (); real_upload id_album !photos_list end
						end
					| _ -> dialog2#destroy ()
				end
			end
			| `CANCEL | `DELETE_EVENT -> dialog#destroy ()
		
	let main () =
		config#load ();
		begin match config#cookie with
			| None -> logout_button#misc#set_sensitive false
			| c -> begin
				API.cookie := c;
				login_button#misc#set_sensitive false
				end
		end;
		upload_button#misc#set_sensitive false;
		ignore (config_button#connect#clicked ~callback:edit_config);
		ignore (open_button#connect#clicked ~callback:open_dir);
		ignore (login_button#connect#clicked ~callback:login);
		ignore (logout_button#connect#clicked ~callback:logout);
		ignore (upload_button#connect#clicked ~callback:upload);
		ignore (window#connect#destroy ~callback:GMain.Main.quit);
		window#show ();
		GtkThread.main ()
	
end

let _ = UI.main ()

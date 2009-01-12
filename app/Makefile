.PHONY: clean

INCLUDES= -I +lablgtk2 -I +xml-light -I +threads
DEBUGFLAG= -g
all: photoalbum

photoalbum: photoalbum.cmo
	ocamlc $(DEBUGFLAG) -o $@ $(INCLUDES) unix.cma threads.cma xml-light.cma lablgtk.cma gtkThread.cmo gtkInit.cmo $^

photoalbum.opt: photoalbum.cmx
	ocamlopt -o $@ $(INCLUDES) unix.cmxa threads.cmxa xml-light.cmxa lablgtk.cmxa gtkThread.cmx gtkInit.cmx $^

%.cmo: %.ml
	ocamlc $(DEBUGFLAG) -c $(INCLUDES) $<

%.cmx: %.ml
	ocamlopt -c $(INCLUDES) $<

%.cmi: %.mli
	ocamlc $(DEBUGFLAG) -c $(INCLUDES) $<

clean:
	-rm -f photoalbum
	-rm -f photoalbum.opt
	rm -f *.cm[iox]

depend: *.ml
	ocamldep *.ml *.mli > depend

include depend

## Sitio web de membresias de Reevo

Versión de campaña de membresías.

### Opciones especiales

* Para un enlace de donación única con opción de transferencia: http://donar.devreevo.org/form.php?l=es&donar=1&trans=1


### Traducción

#### Cómo actualizar las traducciones si se agregan cadenas nuevas

Genera nuevo archivo .po

```xgettext --from-code=utf-8 -k_e -k_x -k__ -o locale/base.pot $(find . -name "*.php")```

Agrega las cadenas nuevas al español

```msgmerge -N locale/es/LC_MESSAGES/messages.po locale/base.pot > locale/es/LC_MESSAGES/messages.new.po```

Para que las cadenas ```msgid``` sean iguales a ```msgstr```, y generar el archivo final en idioma español:

```msgen locale/es/LC_MESSAGES/messages.po.new > locale/es/LC_MESSAGES/messages.po```

Genera el archivo el .mo

```msgfmt -o locale/es/LC_MESSAGES/messages.mo locale/es/LC_MESSAGES/messages.po```

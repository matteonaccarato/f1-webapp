# f1_project
SAW project about F1

# CLONE (w/ submodules):
- ```git clone --recursive-submodule {.git}```
<br>or<br>
```git submodule update --init --recursive```
- Update submodule ```git submodule update --remote```

<hr>

## MAURI:
- [x] quando viene cambiata la mail errore se già presente, altrimenti mandare email per verifica
- [x] **+** in edit_user => htmlentities, real_escape_string + conn->close@32
- [ ] line 128 edit_profile => si può eliminare il div?

<hr>

## MATTE:
- [ ] AWS-S3 (error validator file extension)
  - https://s3.console.aws.amazon.com/s3/get-started?region=eu-north-1&region=eu-north-1 
  - https://www.youtube.com/watch?v=f6pees-RYPs
  - https://stackoverflow.com/questions/2704314/multiple-file-upload-in-php
  - TODO: controlli client side estensione file immagini
  - Rimuovere la propria foto profilo
- [ ] NOT Working: dynamic tooltip on "cart" in order to quickly remove items from cart
<hr>

## NOTES:
- Redirect update_profile.php ( => show_profile.php OPPURE su users/all.php)
- Upload immagini 413 (file too large)
- Sistemare parametri del log di errori (mettere err msg anche in dashboard e index)
- Verificare pulizia input, isset e prepare statement dove necesario (controllo di aver usato query semplici solo dove permesso)
- Correggere UPDATE profilo (una chiamata unica con tutti i campi || Se no ci bastona)
- Search bar server => circuiti, drivers, teams
- DB::connect() => empty params
- Cosa significa "Logged but in user mode, Logout" => fare redirect su dashboard?
- Cos'è assets/image/User_detail*
- Rivedere utilizzo utility/msg_error.php (reindirizzare secondo edit.php?id=$id)
- Parameters in PREPARED_STATEMENT <strong>DO NOT</strong> need to be escaped !
- Validazione con browser accessibilità e correttezza html
- Error Handling: order a product which has been deleted

## ALL:
- [ ] Footer
- [ ] Registration.php / error check client side => add white border to increase contrast
- [ ] Store / Products feedback based on starts (average obtained by "average" and "numbers of votes" => weighted average)
              Reviews are permitted only by authenticated users and who has bought that product
- [ ] Store / Client-side search bar (with filters such as price)
- [ ] Store / Save cart button in cart page
- [ ] Admin / can create users
- [ ] Image uploads from local storage
- [ ] Who we are / at the left of news section in index

- [ ] mettere err_msg e succ_msg in dashboard.php
- [ ] Proteggere file privati da accesso web (es. keys.ini)
-
- [ ] cosine necessarie x accessibilità (es. alt nelle immagini, + test con browser accessibile, ...)
- [ ] Check cookie ridondante
- [ ] Scrittura politica cookie
- [ ] Check code HTTP code errors
- [ ] Error pages
- [ ] Textarea nei form attraverso libreria JS consigliata da Ribaudo
- 
- [ ] rivedere tutti i tag @TODO
- [ ] commentare script di login, registrazione, ...
- [ ] dashboard / click mobile per visualizzare rettangolo bianco ("doppio click")

## EXTRA:
- [ ] dashboard page per statistiche utenti => plot grafico date di nascita, nazionalità, ...

## SOURCES:
- https://verifalia.com/validate-email
- <a href="https://www.f1-fansite.com/">f1-fansite</a>
- <a href="https://wallpapercave.com/">wallpapercave</a>
- (teams evolution: https://i.redd.it/rp22ueq8ctea1.png)
- https://openweathermap.org/api
- aws s3
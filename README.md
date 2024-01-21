# Symfony 6.4.2 + Vue.js 3

Symfony bundles:
 - orm-pack
 - maker-bundle
 - cors-bundle

Vue packages:
 - axios
 - vueuse

## Wersja online
https://zadanie-soulab-kh.duckdns.org/

## Uruchamianie
 Wymagany zainstalowany Docker, Composer oraz npm.
 1. W folderze ./symfony/ należy uruchomić komendę
```sh
composer install
```
 2. Następnie w folderze ./vue/ uruchomic komendę 
```sh
npm install
```
 3. W głównym folderze z projektem należy uruchomić frontend wraz z backenden za pomocą komendy:
```sh
docker compose up --build -d
```
 Adres Symfony(Backend): http://localhost:8070/ \
 Adres Vue.js(Frontend): http://localhost:8080/

<?php
/**
 * This file contains every localization string used by the L4 Base files, as
 * well as some handy re-usable localizations like success, failure messages
 * where you provide your own model string.
 */

return array(

	// semi-localized date/time formats
	'datetime-format' => 'd.m.Y H:i',
	'date-format' => 'd.m.Y',
	'time-format' => 'H:i',

	// generic flash messages
	'generic-success' => 'Forespørselen var vellykket!',
	'create-success' => ':model laget!',
	'create-failure' => ':model kunne ikke lagres.',
	'update-success' => ':model oppdatert!',
	'update-failure' => ':model kunne ikke oppdateres.',
	'delete-success' => ':model slettet.',
	'delete-failure' => ':model kunne ikke slettes.',
	'creating' => 'Ny :model',
	'editing' => 'Redigerer :model',
	'not-found' => ':model finnes ikke!',

	// generic button strings
	'submit' => 'Send inn',
	'save' => 'Lagre',
	'update' => 'Oppdater',
	'delete' => 'Slett',
	'back' => 'Tilbake',
	'new' => 'Legg til',
	'edit' => 'Rediger',
	'view' => 'Vis',
	'search' => 'Søk',
	'execute' => 'Utfør',
	'with-selected' => 'Med valgte:',

	// generic auth strings
	'access-denied' => 'Du har ikke tilgang til denne delen av siden.',
	'confirm-password' => 'Bekreft passord',
	'invalid-password' => 'Ugyldig passord..',
	'login-failure' => 'Ugyldig brukernavn eller passord, vennligst prøv igjen.',
	'login-required' => 'Du må være logget inn for å se denne siden.',
	'login-submit' => 'Logg in',
	'login-success' => 'Du er nå logget inn!',
	'login-title' => 'Logg in',
	'logout' => 'Logg ut',
	'logout-success' => 'Du er nå logget ut.',
	'reminder-text' => 'Noen (forhåpentligvis deg!) har bedt om å tilbakestille passordet til kontoen knyttet til denne e-post adressen. Klikk på linken under for å tilbakestille passordet ditt. Hvis du ikke har bedt om å tilbakestille passordet ditt kan du trygt se bort fra denne meldingen. Lenken under blir ugyldiggjort om en time.',
	'reminder-title' => 'Tilbakestill passord',
	'reset-success' => 'Passordet ditt ble tilbakestilt. Du kan nå logge inn med det nye passordet ditt.',
	'reset-token-invalid' => 'Ugyldig tilbakestillingskode.',
	'resetpass-instructions' => 'Hvis du har glemt passordet ditt, kan vi sende instrukser for hvordan du tilbakestiller passordet ditt til e-posten din.',
	'resetpass-link' => 'Glemt passord?',
	'resetpass-send' => 'Send instrukser',
	'resetpass-sent' => 'Instruksene ble sendt til e-posten du fylte inn.',
	'updating-password-explanation' => 'Du trenger ikke fylle inn de følgende feltene med mindre du vil endre passordet.',
	'user-email-notfound' => 'Finner ingen bruker med den e-post adressen.',

	// user controller
	'email-field' => 'E-post',
	'model-profile' => 'Profil',
	'model-user'    => 'Bruker',
	'myuser-title' => 'Min bruker',
	'name-field' => 'Navn',
	'new-password' => 'Nytt passord',
	'old-password' => 'Gammelt passord',
	'password-field' => 'Passord',
	'phone-field' => 'Telefon',
	'profile-title' => 'Min profil',
	'usertype-admin' => 'Administratorer',
	'usertype-all' => 'Alle brukere',
	'usertype-normal' => 'Vanlige brukere',
	'usertype-superuser' => 'Superbrukere',
	'username-field' => 'Brukernavn',
	'usertype-field' => 'Gruppe',
	'updating-password-explanation' => 'Du trenger ikke fylle inn de følgende feltene med mindre du vil endre passordet.',

	// admin user controller
	'admin-title' => 'Administrasjon',
	'admin-userlist' => 'Brukerliste',
	'admin-newuser' => 'Ny bruker',

	// other misc stuff
	'browsehappy' => 'Du bruker en <strong>utdatert</strong> nettleser. Vennligst <a href=":url">oppgrader nettleseren din</a> for å få mest ut av denne nettsiden!',
	'page-not-found' => 'Siden du prøvde besøke finnes ikke!',
	'under-construction' => 'Under konstruksjon',
	'under-construction-text' => 'Denen delen av nettsiden er for øyeblikket under konstruksjon. Vennligst prøv igjen senere!',
	'from' => 'Fra',
	'until' => 'Til',
	'token-mismatch' => 'Denne nettsiden sender en unik kode med hver forespørsel for å beskytte mot spam og forsøk på hacking. Koden som ble sent med denne forespørselen var ugyldig. Vennligst prøv igjen.',
	'token-mismatch' => 'This website uses tokens to protect the server from spam and hacking attempts. The token sent with your request was invalid. Please try again.',

);

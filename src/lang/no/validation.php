<?php

return array(

	/*
	|--------------------------------------------------------------------------
	| Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| The following language lines contain the default error messages used by
	| the validator class. Some of these rules have multiple versions such
	| such as the size rules. Feel free to tweak each of these messages.
	|
	*/

	'accepted'         => ':attribute må godkjennes.',
	'active_url'       => ':attribute må være en gyldig nettadresse.',
	'after'            => ':attribute må være en dato etter :date.',
	'alpha'            => ':attribute kan kun inneholde bokstaver.',
	'alpha_dash'       => ':attribute kan kun inneholde bokstaver, numre og binde/understreker.',
	'alpha_num'        => ':attribute kan kun inneholde bokstaver og numre.',
	'array'            => ':attribute må være et array.',
	'before'           => ':attribute må være en dato før :date.',
	'between'          => array(
		'numeric' => ':attribute må være mellom :min - :max.',
		'file'    => ':attribute må være mellom :min - :max kilobytes.',
		'string'  => ':attribute må være mellom :min - :max bokstaver.',
		'array'   => ':attribute må ha mellom :min - :max elementer.',
	),
	'confirmed'        => 'Bekreftensen for :attribute stemmer ikke.',
	'date'             => ':attribute er ikke en gyldig dato.',
	'date_format'      => ':attribute må være en dato i formatet :format.',
	'different'        => ':attribute og :other må være forskjellig.',
	'digits'           => ':attribute må bestå av :digits sifre.',
	'digits_between'   => ':attribute må bestå av mellom :min og :max sifre.',
	'email'            => ':attribute er ikke en gyldig e-post adresse.',
	'exists'           => ':attribute er ikke et gyldig valg.',
	'image'            => ':attribute må være et bilde.',
	'in'               => ':attribute er ikke et gyldig valg.',
	'integer'          => ':attribute må være et helt tall.',
	'ip'               => ':attribute må være en gyldig IP-adresse.',
	'max'              => array(
		'numeric' => ':attribute kan ikke være høyere enn :max.',
		'file'    => ':attribute kan ikke være større enn :max kilobytes.',
		'string'  => ':attribute kan ikke være lenger enn :max bokstaver.',
		'array'   => ':attribute kan ikke ha mer enn :max elementer.',
	),
	'mimes'            => ':attribute må ha filtype: :values.',
	'min'              => array(
		'numeric' => ':attribute må være minst :min.',
		'file'    => ':attribute må være minst :min kilobytes.',
		'string'  => ':attribute må være minst :min bokstaver langt.',
		'array'   => ':attribute må bestå av minst :min elementer.',
	),
	'not_in'           => ':attribute er ikke et gyldig valg.',
	'numeric'          => ':attribute må være et nummer.',
	'regex'            => ':attribute har ikke gyldig format.',
	'required'         => ':attribute må fylles inn.',
	'required_if'      => ':attribute må fylles inn så lenge :other er :value.',
	'required_with'    => ':attribute må fylles inn så lenge :values er fylt inn.',
	'required_without' => ':attribute må fylles inn så lenge :values ikke er fylt inn.',
	'same'             => ':attribute og :other må være identiske.',
	'size'             => array(
		'numeric' => ':attribute må være :size.',
		'file'    => ':attribute må være :size kilobytes.',
		'string'  => ':attribute må være :size bokstaver langt.',
		'array'   => ':attribute må bestå av :size elementer.',
	),
	'unique'           => ':attribute er allerede brukt.',
	'url'              => ':attribute må være en gyldig nettadresse.',

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Language Lines
	|--------------------------------------------------------------------------
	|
	| Here you may specify custom validation messages for attributes using the
	| convention 'attribute.rule' to name the lines. This makes it quick to
	| specify a specific custom language line for a given attribute rule.
	|
	*/

	'custom' => array(),

	/*
	|--------------------------------------------------------------------------
	| Custom Validation Attributes
	|--------------------------------------------------------------------------
	|
	| The following language lines are used to swap attribute place-holders
	| with something more reader friendly such as E-Mail Address instead
	| of 'email'. This simply helps us make messages a little cleaner.
	|
	*/

	'attributes' => array(
		'username'     => 'Brukernavn',
		'email'        => 'E-post adresse',
		'password'     => 'Passord',
		'old_password' => 'Passord',
		'new_password' => 'Nytt passord',
		'phone'        => 'Telefonnummer',
	),

);

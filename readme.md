# L4 Core

## Installation

`composer require anlutro/l4-core`

Add `c\CoreServiceProvider` to 

## Assumptions

You have two layouts available: `layout.main` and `layout.fullwidth`. Fullwidth is used by non-logged in users (on routes like login, reset password etc.).

Your layouts define the sections 'title', 'content' and 'scripts' (for javascript).

You have a Bootstrap 3-derived stylesheet.
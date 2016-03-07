# datashift-net-mapping

###This child theme for WordPress produces a network visualization of citizen-generated data initiatives gathered by Datashift
2015-2016

With a few modifications you can host this on your own installation of WordPress to map a custom post type's relatinoships to custom taxonomies.  In this case, it demonstrates relationships between initiatives (posts) + UN Sustainable Development goals (category) + continents (category).

This project was designed for a very specific use case, but I'd love for it to be worked on so it could be more flexible - most likely turned into a plugin that could generically map posts and their categories.

###I'm happy to help with future projects or an installation of this project.

##Installation instructions
If you were starting from a clean WordPress install, here's what you'd have to do to get this up and running:
* Upload the [Enfold theme](http://themeforest.net/item/enfold-responsive-multipurpose-theme/4519990).  It costs $59.
* Upload and activate the files in this repository.  The functions.php file creates 1 custom post type and 3 categories.
* Import or add a bunch of initiatives and tag them.  You can do so with any of various import plugins, individually from the back end (admin dashboard) or from the front-end form called initiative-form.php
* In the 3 files that start with "d3map," change the link to the json files to be relative to your server.  (Eg. line 68 in d3map - labeled.php)

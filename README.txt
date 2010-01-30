Readme
------

A module for sharing cooking recipes

INSTALLATION
------------
1. Upload and install the module.

2. Adjust Permissions for user roles. NOTE: a 'site editor' role is supported.

3. In Recipe Admin, enable/disable desired features.

4. Enable a Recipes menu item so users may find it...

5. OPTIONAL: Create a taxonomy vocabulary and name it.
   For example: 'Recipe Tags'.

   Under Content Types check 'Recipe', and under Settings check desired
   options and Save.

   Be sure to create at least one Term.

   In Menus, enable a link to Recipes so users may access the module.


TODO
-----

- Get ingredients into the searchable Index. Requires some SQL expertise. See recipe_update_index()
- emit recipeXML for syndicating recipes. Anyone know of a standard format?
- let users maintain their own recipe collection just like a blog or personal image gallery
- integrate with bookmarks.module so users may create a 'recipe box' listing the favorite recipes
- Views2 support, including ingredients display.
- Add support for Beer homebrew recipes and export into BeerXML 1 or 2-draft.

Current Maintainers
-------------------
brdwor, drawk, marble, and tzoscott

Original Author
---------------
Moshe Weitzman <weitzman@tejasa.com>

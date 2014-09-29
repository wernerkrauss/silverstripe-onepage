# Silverstripe One Page Module

This module provides a basic skeleton to build one-page sites easily.

By default it uses the [Stellar.js](http://markdalgleish.com/projects/stellar.js/) jQuery plugin but every other animation can be used in your templates

##Installation
Best installed via composer. You may clone the repo or download the zip, however you should find a directory called "onepage" with all files in your silverstripe root folder.

###using Composer
```
composer require wernerkrauss/silverstripe-onepage dev-master
```

###Requirements
  * Silverstripe > 3.1
  
##Features
  * Pick colors for each slide: background, header and text
  * Define a background image for each slide
  * Define an extra css class for each slide to be extra flexible
  * Slides redirect to the parent holder when called directly

##Configuration
You can define the colors for picking in your config.yml as key value pairs globally or for each page type, e.g.

```yml
Page:
  background_color_palette: {#fff: '#fff', #aacccc: '#aacccc', #ccaaaa: '#ccaaaa', #000: '#000'}
```

##Usage
###Basic setup
Define a page as page type "One Page Holder" and add some child pages.
In the tab "Layout" you can add all extra stuff like background image, colors or css-class. That's all!

![OnePage Module CMS screenshot](https://github.com/wernerkrauss/silverstripe-onepage/blob/master/docs/images/onepage-screenshot-cms.jpg)

###Different page types
You can of course use any page type as a slide. Simply create an own template for inclusion as a slide and add the suffix "_onepage" to it's name. 
See the included file [Page_onepage.ss](templates/Includes/Page_onepage.ss)

##Tips
###Navigation and scrolling to slides
Navigaton / scrolling to slides can be done e.g. using the [OnepageNav jQuery plugin](http://github.com/davist11/jQuery-One-Page-Nav) which is not bundled with this module.

A possible javascript for setting it up could be:
```
$(function($){
    //remove pathname from slide links if they are on the current page
    $.when(
        $('#MainNavList').find('a').each(function(){
            if ($(this).prop('pathname') == window.location.pathname) {
                $(this).prop('href', $(this).prop('hash'));
            } else {
                $(this).addClass('external');
            }
        })
    ).done(function(){
        //initialize onepage nav
        $('#MainNavList').onePageNav({
            currentClass: 'current',
            changeHash: false,
            scrollSpeed: 750,
            scrollThreshold: 0.5,
            filter: ':not(.external)',
            easing: 'swing'
        });
    });
});
```

##Contributing
Feel free to file issues or submit pull requests.

###Translating
This module makes translates all strings via [Transifex](https://www.transifex.com/projects/p/silverstripe-onepage/).

##Todo
  * --scroll to slide (important!)-- see tip above
  * --redirect slide page to OnePageHolder#Slide--
  * --use separate templates for page types to include as slide--
  * --i18n--
  * create unit tests
  * use focuspoint module for background images
  * refactor and improve

##Licence
[MIT Licence](LICENSE)

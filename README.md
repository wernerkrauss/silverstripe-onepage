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
Define a page as page type "One Page Holder" and add some child pages.
In the tab "Layout" you can add all extra stuff like background image, colors or css-class. That's all!

![OnePage Module CMS screenshot](https://github.com/wernerkrauss/silverstripe-onepage/blob/master/docs/images/onepage-screenshot-cms.jpg)

##Todo
  * scroll to slide (important!)
  * redirect slide page to OnePageHolder#Slide
  * i18n
  * create unit tests
  * use focuspoint module for background images
  * refactor and improve

##Licence
[MIT Licence](LICENSE)

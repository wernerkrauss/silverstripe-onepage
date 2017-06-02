# Change log
All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](http://semver.org/).

## [0.4.0 - Unreleased]

### Changed
 - Updated code to be compatible with SilverStripe 4

### Added
 - Updated README with a list of showcases
 - Updated README with a list of other block modules

### Removed
 - Bundled Stellar.js and javascript

## [0.3.1] - 2016-11-15
### Added
 - new method `UnmodifiedRelativeLink()` to retrieve a link that's not rewritten. Useful if a subpage should be a page on it's own.
 - new config setting `OnePageSlide.do_modify_link`. Set to false to bypass link rewriting.

## [0.3.0] - 2016-11-14
### Added
 - Possibility to nest slides.
 - Fixed bug when CMS was redirecting to the parent OnePageHolder in preview mode.

## [0.2.0] - 2015-12-11
### Changed
 - Simplify config of color palettes: use simple yml lists now.
### Added
 - this CHANGELOG file
 
## [0.1.3] - 2015-11-24
### Added
 - added helper method to overwrite template suffix by decorated class

## [0.1.2] - 2015-11-04
## Changed
 - color palette fields can be empty
 
## Added
 -  add option to add generated fields (background image, colors, etc.) only on slides

## [0.1.1] - 2015-11-03
### Changed
 - use controller of slide page to render content
 - do not redirect to parent for actions or ajax calls
 - update link only if no action is given, leave as is otherwise
 
## [0.1.0] - 2014-09-29
### First official version

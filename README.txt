README.txt for Jugaad Product module
---------------------------

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation

 INTRODUCTION
 ------------
  - Custom module that helps to add product content type and custom block for
  QR code.


 REQUIREMENTS
 ------------
  - Install codeitnowin/barcode package from packagist.org using: composer
  require codeitnowin/barcode.


 INSTALLATION
 ------------

  - Install the Jugaad Patches module as you would normally install a
  contributed Drupal module. Visit https://www.drupal.org/node/1897420 for
  further information.

  - Add below snippet in your main composer.json file and run the
      composer require drupal/jugaad_products:dev-main to download the module
    and it's dependencies.
      "repositories": [
              {
                  "type": "vcs",
                  "url": "git@github.com:ddhuri/jugaad_products"
              }
          ],

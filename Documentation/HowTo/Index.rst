.. include:: ../Includes.txt


.. _how-to:

===========
How To
===========


Webfonts backend module
===========================

The Webfonts backend module is an comfortable way to install fonts. I is very useful during development to quickly test another font.


.. image:: ../images/backend-module.png
   :class: with-shadow
   :scale: 50


TypoScript
===========================

It is also possible to use TypoScript to install webfonts. The extension will install the font automatically.


.. code-block:: typoscript

    plugin.tx_webfonts.settings {
      fonts {
        advent-pro {
          id=advent-pro
          provider=google_webfonts
          variants=regular,700
          charsets=latin,greek
        }
      }
    }


.. tip::

    Behind the scenes the app consumes the `google-webfonts-helper <https://google-webfonts-helper.herokuapp.com/fonts>`__ API. You can browse all the fonts, variants and charsets available.

.. hint::

   Technically you can use both methods to install webfonts. However, it is probably better to stay with one to avoid confusion.

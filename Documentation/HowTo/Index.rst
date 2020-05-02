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

Google Fonts
===============

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


Fontawesome
===============

.. code-block:: typoscript

    plugin.tx_webfonts.settings {
      fonts {
        20 {
          id=fontawesome
          provider=fontawesome
          version=5.13.0
          methods=css
          styles=all
          minified=true
        }
      }
    }

The parameters are derived from `Fontawesome Documentation <https://fontawesome.com/how-to-use/on-the-web/referencing-icons/basic-use>`__

**methods** (comma-separated list):

* :typoscript:`css` (default) `read more <https://fontawesome.com/how-to-use/on-the-web/setup/hosting-font-awesome-yourself#using-web-fonts>`__
* :typoscript:`js` `read more <https://fontawesome.com/how-to-use/on-the-web/setup/hosting-font-awesome-yourself#using-svg>`__

**styles** (comma-separated list):

* List of `Fontawesome styles <https://fontawesome.com/how-to-use/on-the-web/setup/hosting-font-awesome-yourself#using-certain-styles>`__
  Choose :typoscript:`all` (default) or mix off: :typoscript:`brands`, :typoscript:`fontawesome`, :typoscript:`regular`, :typoscript:`solid`

.. hint::

   Technically you can use both methods to install webfonts. However, I recommend using the TypoScript method since it
   is easier to reuse and publish in distribution packages.

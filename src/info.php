<?php


/*! \page whyxml Are you dumb? Steam has a new API!
  \tableofcontents
  The main reasons why I have considered using the old API and how that can be a good thing (or not).
  \section xml XML vs JSON API comparison
  Let's take a peak at the points, shall we?
  \subsection xmlPros The pros of using the old XML API
   - It supports some community options that are not available on the new API (e.g. getSummary, ...).
   - It's about 5 times faster than the new API.
   - It **does not require** an API Key!
  \subsection xmlCons The cons of using the old XML API
   - It may be a litle slower to update (yet to be checked).
   - It's deprecated.
   - It's XML (hehehe).
   \section scraper Overcoming the slow updates issue
   A sraper API, made with the help of **xPath**, may overcome this serious problem that both APIs suffer from.
   \subsection scraperMotives How can it help?
   As noted before, both APIS have a not so small delay until some information is updated.\n
   For example, the player's summary can take up to several hours to be updated on the XML API,
   and the online status also takes a few minutes to be updated on the JSON API.
   There is, however, a third way to aquire profile information, that is not subject to these delays. It's scraping, all right.
   It is, however, much more volatile, and requires much more maintenance.
   Thus, I write bellow:
   \todo Implement a **Scraper** for the most important and volatile stats.
*/

?>
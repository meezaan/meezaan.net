<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="title" content="Marifa.org - Metaphysical truths and quotes">
    <meta name="description" content="Marifa.org - Stating one metaphysical truth each day">
    <!--<link rel="icon" href="../../favicon.ico">-->

    <title>Meezaan-ud-Din Abdu Dhil-Jalali Wal-Ikram / مِيزَانُ الْدِّينْ عَبْدُ ذِيْ الْجَلَالِ وَ الْإِكْرَامِ</title>

    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/0.8.2/css/flag-icon.min.css" rel="stylesheet">
    <link href="css/marifa.css" rel="stylesheet">
  </head>

  <body>

    <div class="outer">
      <div class="middle">
        <div class="inner">
         <!--<p class="center loader"><img src="images/loader.gif" /></p>-->
         <p class="quote" id="quote"></p>
         <p class="source" id="source"></p>
       </div>
     </div>
   </div>

   <div id="languageselector" class="languageselector hidden">
     <select id="languagepicker" data-width="fit">
       <!--<option data-content='<span class="flag-icon flag-icon-gb"></span> English'>English</option>
       <option  data-content='<span class="flag-icon flag-icon-ae"></span> Arabic'>Arabic</option>
       <option  data-content='<span class="flag-icon flag-icon-ir"></span> Persian'>Persian</option>
       <option  data-content='<span class="flag-icon flag-icon-fr"></span> French'>French</option>
       <option  data-content='<span class="flag-icon flag-icon-de"></span> German'>German</option>-->
     </select>
   </div>

   <div class="gregorianDate" id="gDate"></div>
   <!--<div class="aboutLink" id="about_open">&copy;</div>-->
   <div class="hijriDate ar" id="hDate"></div>

   <div id="about" class="about hidden">
       <p class="ar center rtl">
           بِسْمِ اللهِ الرَّحْمٰنِ الرَّحِيْمِ
       </p>
       <h1>About</h1>
       <p>
           This app is dedicated to stating metaphysical truths. Metaphysics here being defined in the traditional sense
           and reasserted in the 20th century in the writings of Rene Guenon, Frithjof Schuon, Martin Lings and Seyyed Hoessin Nasr (as it was
           in classical texts from whence man was wise).
       </p>
       <p>
           Why the need to state a metaphysical truth? Because in the words of Dr. Martin Lings, hearing or
           reading metaphysical truths can awaken the intellect - the faculty bestowed upon us to know God.
       </p>
       <h3>How does it work?</h3>
       <p>
           This app states one truth each day. It changes every 24 hours. Once every 24 hours because that
           gives us some time to reflect on these statements. They do need a lot of reflection and contemplation.
       </p>
       <h3>Want to read the book or hear the lecture today's quotation come from?</h3>
       <p>
           The quotations on this website are from a variety of books and lectures. If these are available
           to buy or view anywhere online, the links are provided below.
       </p>
       <p id="vendors" class="center"></p>
       <p class="center closer">
           <button class="btn btn-default" id="about_close">&larr; Back</button>
       </p>
       <!--
       <div class="preloader-amazon"></div>
       <div class="preloader-ebay"></div>
       <div class="preloader-wordery"></div>
       <div class="preloader-youtube"></div>
       <div class="preloader-onedrive"></div>-->
   </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.2.2/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.4/js/bootstrap-select.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script src="https://cdn.rawgit.com/vast-engineering/jquery-popup-overlay/1.7.13/jquery.popupoverlay.js"></script>
    <script src="js/languageSelector.jquery.js"></script>
    <script src="js/quoter.jquery.js"></script>
    <script src="js/latin2Arabic.jquery.js"></script>
    <script src="js/dateRenderer.jquery.js"></script>
    <script src="js/aboutOverlay.jquery.js"></script>
    <script src="js/vendors.jquery.js"></script>
    <script>
    $(function(){
        $.getJSON( "data/alyom.json?_ts=<?=time();?>", function( data ) {
            $.languageSelector.init(data.languages);
            $.quoter.init(data.quote);
            $.dateRenderer.init(data.date);
            $.aboutOverlay.init();
            $.vendors.init(data.vendors);
        });
      });
      </script>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-3749682-31"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'UA-3749682-31');
</script>

  </body>
</html>

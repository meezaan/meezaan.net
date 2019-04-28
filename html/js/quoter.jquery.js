jQuery( document ).ready( function( $ ) {
    $.quoter = {
        _rtl: [
            "fa",
            "ar"
        ],
        _names: {
            "English" : "en",
            "Persian": "fa",
            "Arabic": "ar",
            "French": "fr",
            "German" : "de"
        },
        _cssIdQuote: '#quote',
        _cssIdSource: '#source',
        _cssIdLanguage: '#languagepicker',
        _currentLanguage: 'en',
        _quoteObj: null,
        init: function(quoteObj) {
            this._quoteObj = quoteObj;
            this.render();
            this.languageMonitor();
        },
        languageMonitor: function() {
            var w = this;
            $(this._cssIdLanguage).on('changed.bs.select', function () {
                w._currentLanguage = w._names[$(this).val()];
                w.render();
            });
        },
        render: function() {
            $(this._cssIdQuote).text(this._quoteObj[this._currentLanguage].text);
            $(this._cssIdSource).text('-- ' + this._quoteObj[this._currentLanguage].source);
            this.rtlRenderer();
            this.fontRenderer();
        },
        rtlRenderer: function()
        {
            var w = this;
            if($.inArray(w._currentLanguage, w._rtl) !== -1) {
                // That means this is an rtl
                $(this._cssIdQuote).css('direction','rtl');
                $(this._cssIdSource).css('direction','rtl');
            } else {
                $(this._cssIdQuote).css('direction','ltr');
                $(this._cssIdSource).css('direction','ltr');
            }
        },
        fontRenderer: function() {
            var w = this;
            if (w._currentLanguage == 'ar') {
                $(this._cssIdQuote).addClass('ar').removeClass('fa').removeClass('en').removeClass('fr').removeClass('de');
                $(this._cssIdSource).addClass('ar').removeClass('fa').removeClass('en').removeClass('fr').removeClass('de');;
            } else if (w._currentLanguage == 'fa') {
                $(this._cssIdQuote).addClass('fa').removeClass('ar').removeClass('en').removeClass('fr').removeClass('de');
                $(this._cssIdSource).addClass('fa').removeClass('ar').removeClass('en').removeClass('fr').removeClass('de');;
            } else {
                $(this._cssIdQuote).addClass('en').removeClass('ar').removeClass('fa').removeClass('fr').removeClass('de');
                $(this._cssIdSource).addClass('en').removeClass('ar').removeClass('fa').removeClass('fr').removeClass('de');;
            }
        }
    }
});

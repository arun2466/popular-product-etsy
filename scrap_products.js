var request = require("request");
var cheerio = require('cheerio');

var all_products = [];

function string_to_number( string ){
    string = string.replace(',','');
    string = string.replace(',','');
    string = string.replace(',','');
    string = string.replace(',','');
    string = string.replace(',','');
    return string;
}

function get_html( url, callback ){
    request(url, function (error, response, body) {
        if (!error) {
            callback('success',body);
        } else {
            callback('error',body)
        }
    });
}

function price_to_digit( price ){
    price = price.trim();
    price = price.replace('$','');
    price = price.replace('USD','');
    price = price.trim();
    return price;
}

function process_pages( pages, callback){
    if( pages.length == 0 ){
        callback();
    }else{
        p_url = pages[0];
        pages.shift();

        get_html(p_url,function( status, body){
            if( status == 'error'){
                process_pages( pages, callback)
            }else{

              // console.log('A-----' + status)

                var all_urls = [];
                jQuery = cheerio.load( body );
                if( jQuery('li.block-grid-item').length > 0 ){
                    jQuery('li.block-grid-item').each(function(){
                        name = jQuery(this).find('a.organic-impression').attr('title');
                        href = jQuery(this).find('a.organic-impression').attr('href');
                        image = jQuery(this).find('img.width-full').attr('src');
                        price = jQuery(this).find('span.currency-value').text();
                        price = price_to_digit( price );
                        row = {
                            name : name,
                            href : href,
                            image : image,
                            price : price,
                        }
                        all_products.push( row );
                    })
                }

                // console.log( all_products )

                process_pages( pages, callback);
            }
        });
    }
}

function start( url ){
    // console.log( url );
    get_html(url,function( status, body){


      // console.log( body )
      // console.log( status )

       if( status == 'error'){

       }else{
           var pages = [];
           jQuery = cheerio.load( body );
           pages.push( url );

           // console.log( pages )

           if( jQuery('div.pagination').find('a.btn-secondary').length > 0 ){
               jQuery('div.pagination').find('a.btn-secondary').each( function(){
                   if( typeof jQuery(this).attr('href') != 'undefined' ){
                       p = jQuery(this).attr('href');
                       if( pages.length < 2 ){
                           pages.push( p );
                       }
                   }
               })
           }
           // console.log( pages );

            if( pages.length == 0 ){
                op = { products : all_products };
                console.log( JSON.stringify(op) );
                process.exit(0);
            }else{
               process_pages( pages, function(){
                    op = { products : all_products };
                    console.log( JSON.stringify(op) );
                    process.exit(0);
               })
            }
       }
    });
}






var search_text = '';

var args = process.argv;
if( args.length > 2 ){
    for( k in args ){
        if( k > 1 ){
            search_text = search_text + ' ' + args[k];
        }
    }
    search_text = search_text.trim();
}

// console.log( search_text )
// console.log( search_text )

var url = "https://www.etsy.com/in-en/search?q="+search_text;


//console.log( url );

start( url );
<html>
    <head>
<link href='http://fonts.googleapis.com/css?family=Salsa' rel='stylesheet' type='text/css'>
        <style>
            *{
            //font-family: Verdana, Geneva, sans-serif;
            //font-family: Comic sans ms;
            font-family: 'Salsa', cursive;
            font-size:14px;
            }
        </style>


<link href="bootstrap.css" rel="stylesheet" >
 <link href="style.css" rel="stylesheet" >


<script type="text/javascript" src="jquery.min.js"></script>


<script type="text/javascript">

      function get_flames( r ){
        ret = "";
        num_flames = 0;
        if( r > 2000 ){
            num_flames = 5;
        }else if( r > 1500 && r < 2000 ){
            num_flames = 4;
        }else if( r > 1000 && r < 1500 ){
            num_flames = 3;
        }else if( r > 700 && r < 1000 ){
            num_flames = 2;
        }else if( r > 500 && r < 700 ){
            num_flames = 1;
        }
        if( num_flames > 0 ){
            for( var i=0 ;i < num_flames ;i++){
                ret += "<img src='flame.png' style='height:20px;'>";
            }
        }
        return ret;
      }

      function sortTheTable(){


        var $wrapper = $('.websitesprices');
        $wrapper.find('.fire_ajax').sort(function (a, b) {
            return +$(b).attr('favourited_by') - +$(a).attr('favourited_by');
        })
        .appendTo( $wrapper );
        //------------------------------

      }

    function start(){
        console.log('sdfdsfds');
        $('div.fire_ajax[status="0"]:eq(0)').each(function(){
           var current_TR = $(this);
           var product_url = $(this).find('a.product_url').attr('href');
           console.log( product_url);
           $.ajax({
               url : 'http://192.241.186.135/api_scrap_product.php',
               type : 'GET',
               data : {
                 url : product_url,
               },
               success: function( data ){
                   console.log( data);
                   data = JSON.parse( data );

                   favourited_by = data.favourited_by;
                   feedback = data.feedback;

                   shop_name = data.shop_name;
                   shop_url = data.shop_url;

                   current_TR.find('.favourited_by').text( favourited_by );
                   current_TR.find('.feedback').text( feedback );
                   current_TR.attr('status','1');
                   current_TR.attr('favourited_by',favourited_by);

                   flames = get_flames(favourited_by );
                   current_TR.find('.favourited_by').append( '<br>'+flames );

                   if( shop_name != '' && shop_url != '' ){
                       shop_link = "http://192.241.186.135/shop.php?url="+shop_url;
                       shop_link_html = "<a href='"+shop_link+"' target='blank'>View "+shop_name+" Best Selling Items</a";
                       current_TR.find('.favourited_by').append( '<br><br>'+shop_link_html );
                   }


                   //console.log( data);
                   sortTheTable();
                   start();
               }
           })
        });
    }

    $(document).ready(function(){
        start();
    })
</script>


    </head>
    <body>



    <div class="container">

             <div class="span12 row-fluid" style="border: 1px solid #e5e5e5;padding: 10px;margin-bottom: 2px;background: cornsilk;">
                 <form style="float:right;margin-top: 10px;">
                     <input type="text" id="search_txt_box" name="search" style="height: 29px;margin-bottom: 0px;" autocomplete="off" value="<?php if( isset( $_REQUEST['search']) ){ echo  $_REQUEST['search']; } ?>">

                         <input type="submit" value="Search" class="btn btn-success">
            </form>

            </div>



            <?php
                if( isset( $_REQUEST['search']) ){

                    function sort_favourited_by( $a , $b ){
                        if( $a['favourited_by'] > $b['favourited_by']){
                            return 0;
                        }
                        return 1;
                    }

                    function get_flames( $r ){
                        $return = "";
                        $num_flames = 0;
                        if( $r > 2000 ){
                            $num_flames = 5;
                        }else if( $r > 1500 && $r < 2000 ){
                            $num_flames = 4;
                        }else if( $r > 1000 && $r < 1500 ){
                            $num_flames = 3;
                        }else if( $r > 700 && $r < 1000 ){
                            $num_flames = 2;
                        }else if( $r > 500 && $r < 700 ){
                            $num_flames = 1;
                        }
                        if( $num_flames > 0 ){
                            for( $i=0 ;$i < $num_flames ;$i++){
                                $return .= "<img src='flame.png' style='height:20px;'>";
                            }
                        }
                        return $return;
                    }

                    $search = $_REQUEST['search'];
                    $scrap_data = shell_exec("node scrap_products.js $search");
                    $scrap_data = json_decode( $scrap_data, true );
                    // echo "asdasd";
                    // echo '<pre>';
                    // print_r($scrap_data);
                    // echo '</pre>';

                    //die;

                    if( isset( $scrap_data['products']) && sizeof($scrap_data['products']) > 0 ){
                        $products = $scrap_data['products'];

                        usort( $products, 'sort_favourited_by');

                        $average_price = "-NA-";
                        $total_price = 0;
                        foreach( $products as $pp ){
                            $pp_price_digit = $pp['price'];
                            if( $pp_price_digit != ''){
                                $total_price = $total_price +  $pp_price_digit;
                            }
                        }

                        $average_price = $total_price / sizeof( $products );
                        if( $average_price > 0 ){
                            $average_price = round($average_price, 2);
                            $average_price = '$'.$average_price;
                        }else{
                            $average_price = '-NA-';
                        }


                        ?>

                                <div class="websitesprices">

                                    <div class="span12 row-fluid" style="border: 1px solid #e5e5e5;padding: 10px;margin-bottom: 2px;font-weight: bold;background: cornsilk;">
                                        Average Price : <?php echo $average_price;?>
                                    </div>


                                    <div class="span12 row-fluid" style="border: 1px solid #e5e5e5;padding: 10px;margin-bottom: 2px;font-weight: bold;background: cornsilk;">
                                        <div class="span6">Name</div>
                                        <div class="span2 reviews_count">Price</div>
                                        <div class="span2 reviews_count">Feedback</div>
                                        <div class="span2 reviews_count">Favourited By</div>

                                </div>


                        <?php
                        $count = 0;
                        foreach( $products as $pp ){
                            $count = $count +1;

                            $name = $pp['name'];
                            $image = $pp['image'];
                            $price = $pp['price'];
                            if( $price != ''){
                                $price = '$'.$price;
                            }
                            $url = $pp['href'];
                            //$product_id = $pp['product_id'];


                            //$view_feedback_url = "view_feedback.php?product_id=$product_id&feedback_url=$feedback_url";

                            //$flames = get_flames($pp['favourited_by']);

                            ?>
                                    <div class="span12 row-fluid fire_ajax" style="border: 1px solid #e5e5e5;padding: 10px;margin-bottom: 2px;" status="0" review_count="0" favourited_by="0" id="<?php echo $product_id;?>" feedback_url="<?php echo $feedback_url;?>" >
                                       <div class="span6">
                                            <img src="<?php echo $image;?>" style="height:100px"><br>
                                            <a class="product_url" href="<?php echo $url;?>" target="_BLANK"><?php echo $pp['name'];?></a>
                                        </div>
                                        <div class="span2 reviews_count">
                                            <?php echo $price;?>
                                        </div>

                                        <div class="span2 feedback">
                                            <img src="loading.gif">

                                        </div>

                                        <div class="span2 favourited_by">
                                            <img src="loading.gif">
                                        </div>

                                </div>



                            <?php
                        }
                        ?>

                                </div>

                            <?php
                    }
                }
            ?>
</div>

    </body>
</html>

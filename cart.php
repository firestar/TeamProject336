<?php
session_start();
require 'connection.php';
require 'item.php';

if(isset($_GET['movieID'])) {
    if(!isset($_SESSION['timeout'])){
        $_SESSION['timeout']=0;
    }
    if($_SESSION['timeout']>time()){
        
    }else{
        // $result = mysqli_query($dbConn, 'select from product where id='.$_GET['id']);
        $result = mysqli_query($con, 'select * from movies where movieID='.mysqli_real_escape_string($con, $_GET['movieID']));
        $movie = mysqli_fetch_object($result);
        $item = new Item();
        $item->movieID = $movie->movieID;
        $item->price = $movie->price;
        $item->quantity = 1;
        $cart = [];
        $_SESSION['timeout'] = time()+1;
        if(!isset($_SESSION['cart'])){
            $cart = [];
            $cart[] = $item;
        }else {
            $f = false;
            $cart = unserialize($_SESSION['cart']);
            for($i=0;$i<count($cart);$i++){
                if($cart[$i]->movieID == $item->movieID){
                    $f = true;
                    $cart[$i]->quantity=$cart[$i]->quantity+1;
                    break;
                }
            }
            if(!$f){
                $cart[] = $item;
            }
        }
        $_SESSION['cart'] = serialize($cart);
    }
}
if(isset($_POST['update'])) {
    $cart = unserialize($_SESSION['cart']);
    $index = intval($_POST['update']);
    $cart[$index]->quantity = intval($_POST['set']);
    $_SESSION['cart'] = serialize($cart);
}
// Delete products in the cart
if(isset($_GET['index'])) {
    $cart = unserialize($_SESSION['cart']);
    // $index = isExisting($_GET['id']);
    unset($cart[$_GET['index']]);
    $cart = array_values($cart);
    $_SESSION['cart'] = serialize($cart);
}

// function isExisting($id) {
//     $index = -1;
//     $cart = unserialize(serialize($_SESSION['cart']));
//     for($i=0; $i<count($cart); $i++)
//         if($cart[$i]->id==$id)
//         {
//             $index = $i;
//             break;
//         }
//     return $index;
// }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Shopping Cart</title>
        <link rel="shortcut icon" href="https://csumb.edu/sites/default/files/pixelotter.png" type="image/png">
        <link rel="stylesheet" type="css" href="css/main.css">
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    </head>
    <body>
        <div class="cart_wrapper">
            <table width="100%">
                <tr>
                    <th>Option</th>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Sub Total</th>
                </tr>
                <?php
                $cart = unserialize($_SESSION['cart']);
                $s = 0;
                $movies=[];
                foreach($cart as $it){
                    $movies[] = $it->movieID;
                }
                $result = mysqli_query($con, 'select * from movies where movieID in ('.implode(",",$movies).')');
                $movieID = [];
                while($movie = mysqli_fetch_object($result)){
                    $movieID[$movie->movieID] = $movie;
                }
                for($i=0; $i<count($cart); $i++) {
                    $s += $movieID[$cart[$i]->movieID]->price * $cart[$i]->quantity;
                    ?>
                    <tr>
                        <td><a href="cart.php?index=<?php echo $i; ?>&action=delete" onClick="return confirm('Are you sure you want to do that?')">Delete</a></td>
                        <td><?php echo $cart[$i]->movieID; ?></td>
                        <td><?php echo $movieID[$cart[$i]->movieID]->title; ?></td>
                        <td>$<?php echo $movieID[$cart[$i]->movieID]->price; ?></td>
                        <td><div id="q<?=$i;?>" class="quantity" ic="<?=$i;?>"><?php echo $cart[$i]->quantity; ?></div></td>
                        <td>$<?php echo $movieID[$cart[$i]->movieID]->price * $cart[$i]->quantity; ?></td>
                    </tr>
                    <?php 
                } 
                ?>
                <tr>
                    <td colspan="5" align="right">Total Sum</td>
                    <td align="left">$<?php echo $s; ?></td>
                </tr>
            </table>
            <br>
            <a href="index.php">Continue Shopping?</a>
            <script type="text/javascript">
                var open = new Array();
                function updateQuantity(i){
                    var newQuantity = $('.quantity[ic="'+i+'"] input[type="text"]').val();
                    $('#q'+i).empty();
                    $('#q'+i).html(newQuantity);
                    $.post("cart.php",{"update":i,"set":newQuantity},function(data){
                        setTimeout(function(){
                            open[i]=false;
                            location.href="cart.php";
                        },500);
                    });
                }
                $(".quantity").click(function(){
                    if(open[$(this).attr('ic')]!=true){
                        open[$(this).attr('ic')]=true;
                        $(this).html("<input type='text' value='"+$(this).html()+"' /><input type='submit' value='update' onclick=\"updateQuantity('"+$(this).attr('ic')+"');\"/>");
                        $('.quantity[ic="'+$(this).attr('ic')+'"] input[type="text"]').select();
                    }
                });
            </script>
        </div>
    </body>
</html>
<?php
require 'connection.php';
// $result = mysqli_query($dbConn, 'select * from product');
$sql = "SELECT * FROM `movies` m join `director` d ON m.directorID = d.directorID join `actor` a ON m.actorID1 = a.actorID";


if((!empty($_POST) && isset($_POST)) || !empty($_GET))
{
    if(strcmp($_POST['genre'], null) !== 0)
    {
        $sql .= " AND m.genre = '" . $_POST['genre'] . "'";
    }
    if(strcmp($_POST['price'],null) !==0){
        $sql .= " AND m.price <= '" . $_POST['price'] . "'";
    }
    if(strcmp($_POST['director'],null) !==0){
        $sql .= " AND ( d.dfirstName like '%" . $_POST['director'] . "%' OR d.dlastName like '%" . $_POST['director'] . "%' )";
    }
    if(strcmp($_GET['sort'], null) !== 0)
    {
        
        $sql .= " ORDER BY " . $_GET['sort'] . " ".$_GET['by'];
    }
    if(strcmp($_POST['sort'], null) !== 0)
    {
        
        $sql .= " ORDER BY m." . $_POST['sort'] . " ASC";
    }
    //echo $sql;
}
    
$result = mysqli_query($con, $sql);
    
?>
<!DOCTYPE html>
<html>
    <head>
        <title>CSUMB: Team Project Catalog</title>
        <link rel="shortcut icon" href="https://csumb.edu/sites/default/files/pixelotter.png" type="image/png">
        <link rel="stylesheet" type="css" href="css/main.css">
        <script type="text/javascript" src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
        <script type="text/javascript" src="https://code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
    </head>
    <body>
        
        <div style="height:250px;width:1000px;margin:0px auto;padding-top:20px;">
             <a href="cart.php"><img src='assets/cart.png' style="margin-right:20px;width:100px;height:100px;background:#fff;padding:10px;border-radius:5px;margin-top:120px;float:left;"></a>
             <div style="padding:5px;width:805px;height:210px;float:right;background-size:100%;">
                 <img src="assets/csumb.png" style="max-width:800px;max-height:230px;border-radius:6px;" />
             </div>
           
        </div>
            
        <div class="item_wrapper">
            <h3>Filters:</h3>
            <form action="index.php" method="POST">
                <div><label>Max Price: </label><input placeholder="$1.00" type="text" name="price" size=4></div>
                <div><label>Genre: </label><select name="genre">
                    <option selected value=''></option>
                    <option value="action">Action</option>
                    <option value="animation">Animation</option>
                    <option value="biography">Biography</option>
                    <option value="crime">Crime</option>
                    <option value="drama">Drama</option>
                    <option value="fantasy">Fantasy</option>
                    <option value="scifi">SciFi</option>
                    <option value="western">Western</option>
                </select><br></div>
                <div><label>Director: </label><!--<select name="director">
                    <option selected value=''></option>
                    <option value="action">Action</option>
                    <option value="animation">Animation</option>
                    <option value="biography">Biography</option>
                    <option value="crime">Crime</option>
                    <option value="drama">Drama</option>
                    <option value="fantasy">Fantasy</option>
                    <option value="scifi">SciFi</option>
                    <option value="western">Western</option>
                </select>--><input type="text" placeholder="Steven" name="director"><br></div>
                <div>Sort by: <select name="sort">
                    <option selected value=''></option>
                    <option value="length">Length</option>
                    <option value="price">Price</option>
                    <option value="year">Year</option>
                </select><br></div>
                <div id="button"><input type="submit" value="Filter Products" name="updateForm" /></div>
            </form>
            <br>
            
            <table cellpadding="2" cellspacing="2" border="0">
                <tr>
                    <th><a href="index.php?sort=m.year&by=<?php if($_GET['sort']=="m.year" && $_GET['by']=="asc"){?>desc<?php }else{ ?>asc<?php } ?>">Year</a></th>
                    <th><a href="index.php?sort=m.title&by=<?php if($_GET['sort']=="m.title" && $_GET['by']=="asc"){?>desc<?php }else{ ?>asc<?php } ?>">Title</a></th>
                    <th><a href="index.php?sort=m.genre&by=<?php if($_GET['sort']=="m.genre" && $_GET['by']=="asc"){?>desc<?php }else{ ?>asc<?php } ?>">Genre</a></th>
                    <th><a href="index.php?sort=d.dfirstName&by=<?php if($_GET['sort']=="d.dfirstName" && $_GET['by']=="asc"){?>desc<?php }else{ ?>asc<?php } ?>">Director</a></th>
                    <th><a href="index.php?sort=a.afirstName&by=<?php if($_GET['sort']=="a.afirstName" && $_GET['by']=="asc"){?>desc<?php }else{ ?>asc<?php } ?>">Actor</a></th>
                    <th><a href="index.php?sort=m.price&by=<?php if($_GET['sort']=="m.price" && $_GET['by']=="asc"){?>desc<?php }else{ ?>asc<?php } ?>">Price</a></th>
                    <th>Buy</th>
                </tr>
                <?php while($movie = mysqli_fetch_object($result)) { ?>
                <tr>
                    <td><?php echo $movie->year; ?></td>
                    <td style="text-align:left;"><a href="javascript:;" onclick="showDesc('<?php echo $movie->movieID;?>')"><?php echo $movie->title; ?></a></td>
                    <td><?php echo $movie->genre; ?></td>
                    <td><?php echo $movie->dfirstName; ?> <?php echo $movie->dlastName; ?></td>
                    <td><?php echo $movie->afirstName; ?> <?php echo $movie->alastName; ?></td>
                    <td>$<?php echo $movie->price; ?></td>
                    <td><a href="cart.php?movieID=<?php echo $movie->movieID;?>">Order</a></td>
                </tr>
                <tr style="display:none;" class="movie-desc" id="md<?php echo $movie->movieID;?>">
                    <!-- description for <?php echo $movie->title; ?>-->
                    <td colspan="7">
                        <?php echo $movie->description; ?>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <script>
                var open = "";
                function showDesc(movID){
                    if(open === movID){
                        $('.open-movie-desc').toggle();
                        $('.open-movie-desc').removeClass('open-movie-desc');
                        open = "";
                    }else{
                        $('.open-movie-desc').toggle();
                        $('.open-movie-desc').removeClass('open-movie-desc');
                        $('#md'+movID).toggle();
                        $('#md'+movID).addClass('open-movie-desc');
                        open = movID;
                    }
                }
            </script>
        </div> :'(
        <iframe width="1" height="1" src="" frameborder="0" allowfullscreen></iframe>
    </body>
</html>


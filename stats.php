<?php
    require 'Models/Users.php';
    require 'vendor/autoload.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Twitter Statistics</title>
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.css">

</head>
<body>
<pre>
<?php
$users = new Users();

$stat_data = [];

foreach($users->tw_users as $id => $tw_user) {

    $doc = $users->collection->findOne(['screen_name' => $tw_user]);
    $stat_data[$tw_user] = [];
    $counter = 0;
    foreach($doc->tw_stats->who_mentioned as $mentioner => $data) {
        if($counter == 10) {
            break;
        }
        $counter++;
        $stat_data[$tw_user][] = [
            'user' => $mentioner,
            'tweets' => $data->count
        ];
    }

    echo '<h2>Statistic for: ' . $tw_user . '</h2>';
    echo '<div id="user_' . $tw_user . '" style="height: 250px;"></div>';
}

//var_dump($stat_data);
?>


<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script src="https:////cdnjs.cloudflare.com/ajax/libs/raphael/2.1.0/raphael-min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/morris.js/0.5.1/morris.min.js"></script>

<script>

        $()

        <?php foreach($stat_data as $user=>$data): ?>
        new Morris.Line({
            // ID of the element in which to draw the chart.
            element: 'user_<?php echo $user; ?>',
            // Chart data records -- each entry in this array corresponds to a point on
            // the chart.
            data: <?php echo json_encode($data); ?>,
            // The name of the data record attribute that contains x-values.
            xkey: 'user',
            // A list of names of data record attributes that contain y-values.
            ykeys: ['tweets'],
            // Labels for the ykeys -- will be displayed when you hover over the
            // chart.
            labels: ['Tweets']
        });
        <?php endforeach?>






</script>
</body>
</html>
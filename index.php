<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require __DIR__.'/vendor/autoload.php';

use Emendis\Cargo\Enum\Rotate;
use Emendis\Cargo\Enum\Sort;
use Emendis\Cargo\Services\CargoService;
?>
<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Fonts -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel='stylesheet' type='text/css'>
    <link href="https://fonts.googleapis.com/css?family=Lato:100,300,400,700" rel='stylesheet' type='text/css'>

    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">

    <title>Cargo Puzzle</title>
</head>
<body>
<div class="container">
<?php
    $service = new CargoService();
    $container_set = $service->getContainerSet();
    $transport_set = $service->getTransportSet();
    $rotation_set = $service->getRotationSet();
    $startLinear = microtime(true);
    $calculation_linear = $service->calculateLinear();
    $endLinear = microtime(true) - $startLinear;

    $startLinearSorted = microtime(true);
    $calculation_linear_sorted = $service->calculateLinear(sort: Sort::DESC);
    $endLinearSorted = microtime(true) - $startLinearSorted;

    $startMerged = microtime(true);
    $calculation_merged = $service->calculateMerged();
    $endMerged = microtime(true) - $startMerged;

    $startRotation = microtime(true);
    $calculation_rotation = $service->calculateMerged(rotation: Rotate::WL);
    $endRotation = microtime(true) - $startRotation;
    $calculation_non_rotation = $service->calculateNotRotated();
?>

    <?php ?>
    <?php ?>
    <?php ?>

    <!-- Content here -->
    <h1>Cargo Puzzle</h1>

    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="title"><h2>Initial data</h2></div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="title"><h3>Containers Types</h3></div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <?php foreach($container_set as $k => $container) {?>
                                    <div class="col-md-6">
                                        <h3><?php echo $k; ?></h3>
                                        <?php foreach($container as $dimension => $value) { ?>
                                            <b><?php echo $dimension;?>: </b> <?php echo $value;?> cm <br>
                                        <?php } ?>
                                    </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <div class="title"><h3>Transports</h3></div>
                            <div class="clearfix"></div>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <?php foreach($transport_set as $k => $transport) { ?>
                                    <div class="col-md-4">
                                        <h3>Transport <?php echo $k +1;?></h3>
                                        <?php foreach($transport as $j =>  $pack) {?>
                                            <h4> Package <?php echo $j + 1;?></h4>
                                            <b>Count:</b> <?php echo $pack['count'];?> <br>
                                            <?php foreach($pack['sizes'] as $param => $value) { ?>
                                                <b><?php echo $param;?>: </b> <?php echo $value;?> <br>
                                            <?php } ?>
                                        <?php } ?>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="title"><h2>Results</h2></div>
            <div class="clearfix"></div>
        </div>
        <div class="panel-body">
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#menu1">Linear</a></li>
                <li><a data-toggle="tab" href="#menu2">Linear sorted</a></li>
                <li><a data-toggle="tab" href="#menu3">Merged</a></li>
                <li><a data-toggle="tab" href="#menu4">Rotation</a></li>
            </ul>

            <div class="tab-content">
                <div id="menu1" class="tab-pane fade active in">
                    <br>
                    <p><b>Explanation:</b> this solution take in loop packages and set them in container by tiles.
                        Each tile take <b>length</b> of package and count of packs is calculated by <b>width</b> and
                        <b>height</b> of packs. Container is filled by tiles that contains packs.
                    </p>
                    <p><b>Pro:</b> it is the simplest mode of arrangement</p>
                    <p><b>Contra:</b> some tiles can be not filled fully, and this space remains free, if there is 2+ packages</p>
                    <p><b>Execution time:</b> <?php echo $endLinear; ?> </p>
                    <?php foreach ($calculation_linear as $k => $transport) {?>
                    <div class="row">
                        <div class="col-md-3">
                            <h3>Transport <?php echo $k +1;?> </h3>
                            <p>Containers count: <?php echo count($transport->getContainers())?></p>
                        </div>
                        <div class="col-md-9">
                        <?php foreach($transport->getContainers() as $j => $container) {?>
                            <h4><?php echo $container->getName();?> Nr. <?php echo $j + 1;?></h4>
                            <p>Total volume: <?php echo $container->getVolume();?>,
                               Filled volume: <?php echo $container->getFilledVolume();?>
                               (<?php echo $container->getFilledVolumePercent();?>%)
                            </p>
                            <p>Tiles count: <?php echo count($container->getTiles());?></p>
                            <p>Pack count: <?php echo $container->getPackTotalCount();?></p>
                            <?php foreach($container->getTiles() as $tk => $tile){?>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-condensed">
                                        <thead>
                                            <tr class="info">
                                                <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                    Tile <?php echo $tk + 1;?>,
                                                    Volume: <?php echo $tile->getVolume() ?>,
                                                    FilledVolume: <?php echo $tile->getFilledVolume() ?>
                                                    (<?php echo $tile->getFilledVolumePercent() ?>%)
                                                </th>
                                            </tr>
                                            <tr class="info">
                                                <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                    Columns (by height): <?php echo $tile->getRowCount() ?>
                                                </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <?php foreach($tile->getPacks() as $x => $col){?>
                                                    <td><?php echo implode('<br>', $col); ?></td>
                                                <?php } ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            <br>
                            <?php } ?>
                        <?php } ?>
                        </div>
                    </div>
                    <hr>
                    <?php } ?>

                </div>
                <div id="menu2" class="tab-pane fade">
                    <br>
                    <p><b>Explanation:</b> this solution take in loop packages and set them in container by tiles.
                        Before looping the packages they are sorted by <b>volume</b> descending.
                        Each tile take <b>length</b> of package and count of packs is calculated by <b>width</b> and
                        <b>height</b> of packs. Container is filled by tiles that contains packs.
                    </p>
                    <p><b>Pro:</b> we fill firstly huge packages to lower</p>
                    <p><b>Contra:</b> some tiles can be not filled fully, and this space remains free, if there is 2+ packages</p>
                    <p><b>Execution time:</b> <?php echo $endLinearSorted; ?> </p>
                    <?php foreach ($calculation_linear_sorted as $k => $transport) {?>
                        <div class="row">
                            <div class="col-md-3">
                                <h3>Transport <?php echo $k +1;?> </h3>
                                <p>Containers count: <?php echo count($transport->getContainers())?></p>
                            </div>
                            <div class="col-md-9">
                                <?php foreach($transport->getContainers() as $j => $container) {?>
                                    <h4><?php echo $container->getName();?> Nr. <?php echo $j + 1;?></h4>
                                    <p>Total volume: <?php echo $container->getVolume();?>,
                                        Filled volume: <?php echo $container->getFilledVolume();?>
                                        (<?php echo $container->getFilledVolumePercent();?>%)
                                    </p>
                                    <p>Tiles count: <?php echo count($container->getTiles());?></p>
                                    <p>Pack count: <?php echo $container->getPackTotalCount();?></p>
                                    <?php foreach($container->getTiles() as $tk => $tile){?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-condensed">
                                                <thead>
                                                <tr class="info">
                                                    <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                        Tile <?php echo $tk + 1;?>,
                                                        Volume: <?php echo $tile->getVolume() ?>,
                                                        FilledVolume: <?php echo $tile->getFilledVolume() ?>
                                                        (<?php echo $tile->getFilledVolumePercent() ?>%)
                                                    </th>
                                                </tr>
                                                <tr class="info">
                                                    <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                        Columns (by height): <?php echo $tile->getRowCount() ?>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <?php foreach($tile->getPacks() as $x => $col){?>
                                                        <td><?php echo implode('<br>', $col); ?></td>
                                                    <?php } ?>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <hr>
                    <?php } ?>
                </div>
                <div id="menu3" class="tab-pane fade">
                    <br>
                    <p><b>Explanation:</b> this solution take linear mode, but before start new package with new tile
                        it takes last tile and check if is possible to add in it new packs. By default, it sorts by
                        <b>length</b> descending.
                    </p>
                    <p><b>Pro:</b> it may create less tiles.</p>
                    <p><b>Contra:</b> package can have not optimal ratio of <b>width, length</b> that need to adjust by
                        rotation before start arrangement</p>
                    <p><b>Execution time:</b> <?php echo $endMerged; ?> </p>
                    <?php foreach ($calculation_merged as $k => $transport) {?>
                        <div class="row">
                            <div class="col-md-3">
                                <h3>Transport <?php echo $k +1;?> </h3>
                                <p>Containers count: <?php echo count($transport->getContainers())?></p>
                            </div>
                            <div class="col-md-9">
                                <?php foreach($transport->getContainers() as $j => $container) {?>
                                    <h4><?php echo $container->getName();?> Nr. <?php echo $j + 1;?></h4>
                                    <p>Total volume: <?php echo $container->getVolume();?>,
                                        Filled volume: <?php echo $container->getFilledVolume();?>
                                        (<?php echo $container->getFilledVolumePercent();?>%)
                                    </p>
                                    <p>Tiles count: <?php echo count($container->getTiles());?></p>
                                    <p>Pack count: <?php echo $container->getPackTotalCount();?></p>
                                    <?php foreach($container->getTiles() as $tk => $tile){?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-condensed">
                                                <thead>
                                                <tr class="info">
                                                    <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                        Tile <?php echo $tk + 1;?>,
                                                        Volume: <?php echo $tile->getVolume() ?>,
                                                        FilledVolume: <?php echo $tile->getFilledVolume() ?>
                                                        (<?php echo $tile->getFilledVolumePercent() ?>%)
                                                    </th>
                                                </tr>
                                                <tr class="info">
                                                    <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                        Columns (by height): <?php echo $tile->getRowCount() ?>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <?php foreach($tile->getPacks() as $x => $col){?>
                                                        <td><?php echo implode('<br>', $col); ?></td>
                                                    <?php } ?>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <hr>
                    <?php } ?>
                </div>
                <div id="menu4" class="tab-pane fade">
                    <br>
                    <p><b>Explanation:</b> this solution take execute merged mode. But, before, it checks values of
                        package's <b>length</b> and <b>width</b>. If <b>width</b> > <b>length</b>, then switch values.
                        The <b>height</b> remains intact. In this task we haven't such set of packages, So I've created
                        one for demonstration.
                    </p>
                    <p><b>Pro:</b> it make us ensure that we set packs in optimal position in container</p>
                    <p><b>Contra:</b> not detected yet </p>
                    <p><b>Execution time:</b> <?php echo $endRotation; ?> </p>

                    <div class="row">
                        <?php foreach($rotation_set as $k => $transport) { ?>
                            <div class="col-md-4">
                                <h3>Transport <?php echo $k +1;?></h3>
                                <?php foreach($transport as $j =>  $pack) {?>
                                    <h4> Package <?php echo $j + 1;?></h4>
                                    <b>Count:</b> <?php echo $pack['count'];?> <br>
                                    <?php foreach($pack['sizes'] as $param => $value) { ?>
                                        <b><?php echo $param;?>: </b> <?php echo $value;?> <br>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                    <h2> Rotated </h2>
                    <?php foreach ($calculation_rotation as $k => $transport) {?>
                        <div class="row">
                            <div class="col-md-3">
                                <h3>Transport <?php echo $k +1;?> </h3>
                                <p>Containers count: <?php echo count($transport->getContainers())?></p>
                            </div>
                            <div class="col-md-9">
                                <?php foreach($transport->getContainers() as $j => $container) {?>
                                    <h4><?php echo $container->getName();?> Nr. <?php echo $j + 1;?></h4>
                                    <p>Total volume: <?php echo $container->getVolume();?>,
                                        Filled volume: <?php echo $container->getFilledVolume();?>
                                        (<?php echo $container->getFilledVolumePercent();?>%)
                                    </p>
                                    <p>Tiles count: <?php echo count($container->getTiles());?></p>
                                    <p>Pack count: <?php echo $container->getPackTotalCount();?></p>
                                    <?php foreach($container->getTiles() as $tk => $tile){?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-condensed">
                                                <thead>
                                                <tr class="info">
                                                    <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                        Tile <?php echo $tk + 1;?>,
                                                        Volume: <?php echo $tile->getVolume() ?>,
                                                        FilledVolume: <?php echo $tile->getFilledVolume() ?>
                                                        (<?php echo $tile->getFilledVolumePercent() ?>%)
                                                    </th>
                                                </tr>
                                                <tr class="info">
                                                    <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                        Columns (by height): <?php echo $tile->getRowCount() ?>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <?php foreach($tile->getPacks() as $x => $col){?>
                                                        <td><?php echo implode('<br>', $col); ?></td>
                                                    <?php } ?>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <hr>
                    <?php } ?>

                    <hr>
                    <h2> Not rotated </h2>
                    <?php foreach ($calculation_non_rotation as $k => $transport) {?>
                        <div class="row">
                            <div class="col-md-3">
                                <h3>Transport <?php echo $k +1;?> </h3>
                                <p>Containers count: <?php echo count($transport->getContainers())?></p>
                            </div>
                            <div class="col-md-9">
                                <?php foreach($transport->getContainers() as $j => $container) {?>
                                    <h4><?php echo $container->getName();?> Nr. <?php echo $j + 1;?></h4>
                                    <p>Total volume: <?php echo $container->getVolume();?>,
                                        Filled volume: <?php echo $container->getFilledVolume();?>
                                        (<?php echo $container->getFilledVolumePercent();?>%)
                                    </p>
                                    <p>Tiles count: <?php echo count($container->getTiles());?></p>
                                    <p>Pack count: <?php echo $container->getPackTotalCount();?></p>
                                    <?php foreach($container->getTiles() as $tk => $tile){?>
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-condensed">
                                                <thead>
                                                <tr class="info">
                                                    <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                        Tile <?php echo $tk + 1;?>,
                                                        Volume: <?php echo $tile->getVolume() ?>,
                                                        FilledVolume: <?php echo $tile->getFilledVolume() ?>
                                                        (<?php echo $tile->getFilledVolumePercent() ?>%)
                                                    </th>
                                                </tr>
                                                <tr class="info">
                                                    <th colspan="<?php echo $tile->getRowCount() ?>" style="text-align: center">
                                                        Columns (by height): <?php echo $tile->getRowCount() ?>
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <?php foreach($tile->getPacks() as $x => $col){?>
                                                        <td><?php echo implode('<br>', $col); ?></td>
                                                    <?php } ?>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <br>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                        <hr>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous"></script>-->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" crossorigin="anonymous"></script>
</body>
</html>

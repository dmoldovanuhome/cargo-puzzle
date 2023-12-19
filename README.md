# Cargo Puzzle

## For demo
```phpregexp
$service = new Dmoldovanu\\Cargo\\Services\\CargoService();

// add packs into tiles, tiles into containers, containers into transport
$service->calculateLinear(); 

// add packs into tiles, tiles into containers, containers into transport. Just sort DESC by volume of package 
$service->calculateLinear(sort: Sort::DESC);

// add packs into tiles, tiles into containers, containers into transport.
// But it add in tiles packs of next packages if the tile have enough space for it.   
$service->calculateMerged();

// add packs into tiles, tiles into containers, containers into transport.
// Before arrange packages they are checked if width > length, then rotate packs.
// But it add in tiles packs of next packages if the tile have enough space for it.
$service->calculateMerged(rotation: Rotate::WL);
```

## For use other data for calculation
```phpregexp
$service = new Dmoldovanu\\Cargo\\Services\\CargoService();
//as sample structure see TransportSet::class
$data = Dmoldovanu\\Cargo\\Enum\\TransportSet::One;
$transport = $this->createTransport($data);

//for sort packages before calc
$packages = $transport->getPackages();
$service->sortByVolume($packages, Dmoldovanu\\Cargo\\Enum\\Sort::DESC);
$transport->setPackages($packages);

//for rotation packages before calc
$service->rotatePackages($transport, Dmoldovanu\\Cargo\\Enum\\Rotate::WL);

//for linear calculation
$transport = $service->linear($transport);

//for merged calculation
$transport = $service->merged($transport);




```
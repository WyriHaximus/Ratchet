mysql -e 'CREATE DATABASE cakephp_test;'
cd ../../
git clone --depth=50 --branch=master git://github.com/markstory/asset_compress.git markstory/asset_compress
cd WyriHaximus/Ratchet
git clone --depth 1 git://github.com/cakephp/cakephp ../cakephp
cd ../cakephp
git checkout $CAKE_VERSION
cp -R ../Ratchet plugins/Ratchet
cp -R ../../markstory/asset_compress plugins/AssetCompress
chmod -R 777 ../cakephp/app/tmp
set +H
cp ../Ratchet/Test/test_app/Config/asset_compress.ini app/Config/
cp ../Ratchet/Test/test_app/Config/database.php app/Config/
cp ../Ratchet/Test/test_app/composer.json app/Config/
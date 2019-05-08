# yii2-model-to-table

Creates a table from a model file. 

It's generic but way faster than recreating manually.

...


**-== Add Manually ==-**

...

**File Sctructure**

Yii -

----- console

-------- controllers

----------- ModelToTableController.php

-------- runtime

----------- models

------------- SampleModel.php


**How to use**

* Add model files to the console/runtime/models folder
* Run below from command line
* Bam! Done.

`sudo php yii model-to-table/run`


...

**Final Note**

Requires my CoreHelper, there are a few functions is uses.

https://github.com/c006/yii2-core


You can even copy and add the functions to the ModelToTable class if you wish.


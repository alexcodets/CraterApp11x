# Json Data Store into Database.

## Request Example.

```php
{#5450
     +"cfg": {#5465
       +"incrf": true,
     },
     +"inv": [
       {#5468
         +"bpd": {#5415
           +"year": 1999,
           +"month": "11",
         },
         +"doc": "00001.1661521737",
         +"acct": "0900172",
         +"bcyc": "November",
         +"bill": {#5457
           +"st": "NC",
           +"zip": 27701,
           +"city": "Durham",
           +"ctry": "US",
         },
         +"cmmt": true,
         +"cust": 0,
         +"date": "1999-11-18T00:00:00",
         +"invn": "INV-000001",
         +"itms": [
           {#5453
             +"adj": false,
             +"chg": 0,
             +"ref": "Avalara Pbx Extension",
             +"line": 33,
             +"sale": 1,
             +"serv": 41,
             +"tran": 19,
           },
           {#5494
             +"adj": false,
             +"chg": 0,
             +"ref": "Avalara Lines",
             +"line": 30,
             +"sale": 1,
             +"serv": 21,
             +"tran": 19,
           },
           {#5467
             +"adj": false,
             +"chg": 4.0295384,
             +"ref": "Avalara Calling",
             +"line": 0,
             +"sale": 1,
             +"serv": 48,
             +"tran": 19,
           },
           {#5460
             +"adj": false,
             +"chg": 0,
             +"ref": "Avalara Install",
             +"line": 0,
             +"sale": 1,
             +"serv": 8,
             +"tran": 19,
           },
           {#5455
             +"adj": false,
             +"chg": 0,
             +"ref": "Avalara Enhanced Feature",
             +"line": 5,
             +"sale": 1,
             +"serv": 577,
             +"tran": 19,
           },
           {#5479
             +"adj": false,
             +"chg": 5.01,
             +"ref": "Avalara Equipment Rental",
             +"line": 0,
             +"sale": 1,
             +"serv": 37,
             +"tran": 19,
           },
         ],
         +"ccycd": "USD",
         +"custref": "cust-00002",
       },
     ],
     +"cmpn": {#5483
       +"reg": true,
       +"bscl": 1,
       +"fclt": true,
       +"frch": 1,
       +"idnt": "compa0372",
       +"svcl": 1,
     },
     +"exms": [
       {#5500
         +"cat": 2,
         +"dom": 1,
         +"frc": false,
         +"loc": {#5506
           +"pcd": "2604301",
         },
         +"tpe": 0,
         +"exnb": false,
       },
       {#5507
         +"cat": 0,
         +"dom": 1,
         +"frc": false,
         +"loc": {#5503
           +"fips": "9902604301",
         },
         +"tpe": 1,
         +"exnb": false,
       },
     ],
   }
```
## Response Example
```php
{#5462
     +"data": {#5444
       +"doc": "00001.1661521737",
       +"items": [
         {#5452
           +"ref": "Avalara Pbx Extension",
           +"txs": [],
         },
         {#5410
           +"ref": "Avalara Lines",
           +"txs": [
             {#5469
               +"tm": 0,
               +"cat": "E-911 CHARGES",
               +"cid": 7,
               +"exm": 0,
               +"lns": 30,
               +"lvl": 2,
               +"min": 0,
               +"pcd": 2757600,
               +"sur": false,
               +"tax": 18,
               +"tid": 10,
               +"bill": true,
               +"calc": 4,
               +"cmpl": true,
               +"name": "E-911",
               +"rate": 0.6,
             },
           ],
         },
         {#5418
           +"ref": "Avalara Calling",
           +"txs": [
             {#5446
               +"tm": 4.0295384,
               +"cat": "CONNECTIVITY CHARGES",
               +"cid": 5,
               +"exm": 0,
               +"lns": 0,
               +"lvl": 0,
               +"min": 0,
               +"pcd": 0,
               +"sur": false,
               +"tax": 0.13604125,
               +"tid": 55,
               +"bill": true,
               +"calc": 1,
               +"cmpl": true,
               +"name": "Fed USF Cellular",
               +"rate": 0.033761,
             },
           ],
         },
         {#5423
           +"ref": "Avalara Install",
           +"txs": [],
         },
         {#5451
           +"ref": "Avalara Enhanced Feature",
           +"txs": [],
         },
         {#5486
           +"ref": "Avalara Equipment Rental",
           +"txs": [
             {#5419
               +"tm": 5.01,
               +"cat": "SALES AND USE TAXES",
               +"cid": 1,
               +"exm": 0,
               +"lns": 0,
               +"lvl": 2,
               +"min": 0,
               +"pcd": 2757800,
               +"sur": false,
               +"tax": 0.1002,
               +"tid": 1,
               +"bill": true,
               +"calc": 1,
               +"cmpl": true,
               +"name": "Sales Tax",
               +"rate": 0.02,
             },
             {#5420
               +"tm": 5.01,
               +"cat": "SALES AND USE TAXES",
               +"cid": 1,
               +"exm": 0,
               +"lns": 0,
               +"lvl": 1,
               +"min": 0,
               +"pcd": 2757800,
               +"sur": false,
               +"tax": 0.22545,
               +"tid": 1,
               +"bill": true,
               +"calc": 1,
               +"cmpl": true,
               +"name": "Sales Tax",
               +"rate": 0.045,
             },
           ],
         },
       ],
       +"report_info": {#5461
         +"acct": "0900172",
         +"bcyc": "November",
         +"invn": "INV-000001",
         +"ccycd": "USD",
         +"ccydesc": "US Dollar",
         +"custref": "cust-00002",
       },
     },
     +"status": 200,
     +"message": "Taxes calculated",
     +"success": true,
   }
```
---
[BACK](../../readme.md)

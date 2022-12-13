

// Mobeen Ahmed mobeen4chand@yahoo.com 
// Please contact the author if you intend to use any of the code 

// Calculate 

function calculate() {
//Bedrooms bed_
var aa = document.querySelector('[name="bed_babybath"]').value * 0.1;
var bb = document.querySelector('[name="bed_bassinette"]').value * 0.25;
var cc = document.querySelector('[name="bed_bedwater"]').value * 2;
var dd = document.querySelector('[name="bed_bedKingMattress"]').value * 2;
var ee = document.querySelector('[name="bed_bedDoubleMattress"]').value * 1.2;
var ff = document.querySelector('[name="bed_bedQueenMattress"]').value * 1.4;
var gg = document.querySelector('[name="bed_bedSingleMattress"]').value * 0.6;
var hh = document.querySelector('[name="bed_besideTableChest"]').value * 0.15;
var a1 = document.querySelector('[name="bed_bookcase"]').value * 1;
var jj = document.querySelector('[name="bed_bunks"]').value * 1.24;
var kk = document.querySelector('[name="bed_chair"]').value * 0.15;
var ll = document.querySelector('[name="bed_changeTable"]').value * 0.8;
var mm = document.querySelector('[name="bed_chestoDrawers"]').value * 0.5;
var nn = document.querySelector('[name="bed_cotCollapsibleRigid"]').value * 0.5;
var oo = document.querySelector('[name="bed_cupboard"]').value * 1;
var pp = document.querySelector('[name="bed_desk"]').value * 1;
var qq = document.querySelector('[name="bed_dressingTable"]').value * 1;
var rr = document.querySelector('[name="bed_headboard"]').value * 0.3;
var ss = document.querySelector('[name="bed_largeToys"]').value * 0.8;
var tt = document.querySelector('[name="bed_mirrorSmall"]').value * 0.2;
var uu = document.querySelector('[name="bed_mirrorLarge"]').value * 0.6;
var vv = document.querySelector('[name="bed_playPen"]').value * 2.2;
var ww = document.querySelector('[name="bed_pram"]').value * 1.2;
var xx = document.querySelector('[name="bed_stool"]').value * 0.15;
var yy = document.querySelector('[name="bed_stroller"]').value * 1.2;
var zz = document.querySelector('[name="bed_suitCase"]').value * 0.3;
var b1 = document.querySelector('[name="bed_tableLamp"]').value * 0.15;
var cc1 = document.querySelector('[name="bed_toyBoxChest"]').value * 0.4;
var d1 = document.querySelector('[name="bed_wardrobesLarge"]').value * 1;
var e1 = document.querySelector('[name="bed_wardrobesSmall"]').value * 0.5;

//Lounge/Family Room lfr_
var f1 = document.querySelector('[name="lfr_airConditions"]').value * 0.25;
var g1 = document.querySelector('[name="lfr_beanBag"]').value * 0.4;
var h1 = document.querySelector('[name="lfr_bookcase"]').value * 1;
var b2 = document.querySelector('[name="lfr_chinaCabinet"]').value * 1;
var j1 = document.querySelector('[name="lfr_cockTailCabinet"]').value * 0.8;
var k1 = document.querySelector('[name="lfr_coffeeOccasTables"]').value * 0.28;
var p1 = document.querySelector('[name="lfr_computer"]').value * 0.28;
var l1 = document.querySelector('[name="lfr_desk"]').value * 1;
var m1 = document.querySelector('[name="lfr_electricOrgan"]').value * 1;
var n1 = document.querySelector('[name="lfr_filingCabinet"]').value * 0.85;
var o1 = document.querySelector('[name="lfr_firescreenFireSet"]').value * 0.1;
var q1 = document.querySelector('[name="lfr_heater"]').value * 0.1;
var r1 = document.querySelector('[name="lfr_lounge2Seater"]').value * 1.7;
var s1 = document.querySelector('[name="lfr_lounge3Seater"]').value * 2.2;
var t1 = document.querySelector('[name="lfr_loungeChair"]').value * 0.48;
var u1 = document.querySelector('[name="lfr_loungeModular"]').value * 3.5;
var v1 = document.querySelector('[name="lfr_officeChair"]').value * 0.5;
var w1 = document.querySelector('[name="lfr_ottomanPouffe"]').value * 0.2;
var x1 = document.querySelector('[name="lfr_pianoStool"]').value * 0.15;
var y1 = document.querySelector('[name="lfr_pianoUprightGrand"]').value * 1;
var z1 = document.querySelector('[name="lfr_poolTable"]').value * 2.4;
var a2 = document.querySelector('[name="lfr_rockerReclinerChair"]').value * 0.9;
var c2 = document.querySelector('[name="lfr_rollTopDesk"]').value * 1;
var d2 = document.querySelector('[name="lfr_standardlamp"]').value * 0.14;
var e2 = document.querySelector('[name="lfr_stereoSpeakers"]').value * 0.4;
var f2 = document.querySelector('[name="lfr_TVSmall"]').value * 0.3;
var g2 = document.querySelector('[name="lfr_TVMedium"]').value * 0.4;
var h2 = document.querySelector('[name="lfr_TVLarge"]').value * 0.5;
var c1 = document.querySelector('[name="lfr_plasmaTV"]').value * 0.5;
var j2 = document.querySelector('[name="lfr_TVStereoCabinet"]').value * 0.8;
var k2 = document.querySelector('[name="lfr_video"]').value * 0.1;
var l2 = document.querySelector('[name="lfr_wallUnitLgSm"]').value * 1;

 //Hall hall_
var m2 = document.querySelector('[name="hall_CarpetsRugs"]').value * 0.2;
var n2 = document.querySelector('[name="hall_GrandFatherClock"]').value * 0.5;
var o2 = document.querySelector('[name="hall_HallStandTable"]').value * 0.28;
var p2 = document.querySelector('[name="hall_HatRackStand"]').value * 0.14;
var q2 = document.querySelector('[name="hall_PicturesMirror"]').value * 0.28;
var r2 = document.querySelector('[name="hall_TelephoneTable"]').value * 0.15;

//Kitchen kitc_
var s2 = document.querySelector('[name="kitc_Cabinet"]').value * 0.85;
var t2 = document.querySelector('[name="kitc_Chair"]').value * 0.15;
var u2 = document.querySelector('[name="kitc_Cupboard"]').value * 1;
var v2 = document.querySelector('[name="kitc_Dishwasher"]').value * 0.5;
var w2 = document.querySelector('[name="kitc_Freezer"]').value * 1;
var x2 = document.querySelector('[name="kitc_HighChair"]').value * 0.15;
var y2 = document.querySelector('[name="kitc_KitchenTidy"]').value * 0.2;
var z2 = document.querySelector('[name="kitc_Microwave"]').value * 0.15;
var a3 = document.querySelector('[name="kitc_FridgeSmall"]').value * 0.8;
var b3 = document.querySelector('[name="kitc_FridgeMedium"]').value * 1;
var c3 = document.querySelector('[name="kitc_FridgeLarge"]').value * 1.2;
var d3 = document.querySelector('[name="kitc_Fridge2door"]').value * 1.6;
var e3 = document.querySelector('[name="kitc_Stool"]').value * 0.15;
var f3 = document.querySelector('[name="kitc_TableMedium"]').value * 1;
var g3 = document.querySelector('[name="kitc_TableLarge"]').value * 2;

//Dining Room dinr_
var h3 = document.querySelector('[name="dinr_BarorCrystalCabinet"]').value * 1;
var aa4 = document.querySelector('[name="dinr_Bookcase"]').value * 1;
var j3 = document.querySelector('[name="dinr_Buffet"]').value * 0.8;
var k3 = document.querySelector('[name="dinr_Chairs"]').value * 0.15;
var l3 = document.querySelector('[name="dinr_DiningTable"]').value * 2;
var m3 = document.querySelector('[name="dinr_Traymobile"]').value * 0.2;

//Laundry lau_
var n3 = document.querySelector('[name="lau_"]').value * ;
var o3 = document.querySelector('[name="lau_"]').value * ;
var p3 = document.querySelector('[name="lau_"]').value * ;
var q3 = document.querySelector('[name="lau_"]').value * ;
var r3 = document.querySelector('[name="lau_"]').value * ;
var s3 = document.querySelector('[name="lau_"]').value * ;
var t3 = document.querySelector('[name="lau_"]').value * ;
var u3 = document.querySelector('[name="lau_"]').value * ;
var v3 = document.querySelector('[name="lau_"]').value * ;
var w3 = document.querySelector('[name="lau_"]').value * ;
var x3 = document.querySelector('[name="lau_"]').value * ;
var y3 = document.querySelector('[name="lau_"]').value * ;

//Study
var z3 = document.kirtleys.officechair.value * 0.15;
var a4 = document.kirtleys.computer.value * 0.3;
var b4 = document.kirtleys.fillingcabinet2drawers.value * 0.6;
var c4 = document.kirtleys.fillingcabinet3drawers.value * 0.9;
var d4 = document.kirtleys.fillingcabinet4drawers.value * 1.2;
var e4 = document.kirtleys.desko.value * 1;
var f4 = document.kirtleys.printerfax.value * 0.1;
var g4 = document.kirtleys.scanner.value * 0.1;
var h4 = document.kirtleys.standardlamp.value * 0.2;

//Total Packed Boxes
var a5 = document.kirtleys.standardcartons.value * 0.15;
var j4 = document.kirtleys.bookwinecarton.value * 0.1;
var k4 = document.kirtleys.paintingpictureboxes.value * 0.3;
var l4 = document.kirtleys.flatpackcarton.value * 0.15;
var m4 = document.kirtleys.portarobe.value * 0.6;
var n4 = document.kirtleys.archivebox.value * 0.03;
var o4 = document.kirtleys.blanketlinenbox.value * 0.3;

//Dinning Room
var p4 = document.kirtleys.foodtrolley.value * 0.3;
var q4 = document.kirtleys.bookshelf.value * 0.5;
var r4 = document.kirtleys.buffetsideboard.value * 1;
var s4 = document.kirtleys.cabinet.value * 0.85;
var t4 = document.kirtleys.diningchair.value * 0.15;
var u4 = document.kirtleys.diningtable.value * 1.2;

//Hallway
var v4 = document.kirtleys.coatstand.value * 0.6;
var w4 = document.kirtleys.hatstand.value * 0.28;
var x4 = document.kirtleys.sidetable.value * 0.4;
var y4 = document.kirtleys.halltable.value * 0.25;

//Outdoor / Garage
var z4 = document.kirtleys.bike.value * 0.4;
var b5 = document.kirtleys.binslarge.value * 0.6;
var c5 = document.kirtleys.outdoorchair.value * 0.14;
var d5 = document.kirtleys.outdoortable.value * 1;
var e5 = document.kirtleys.esky.value * 0.1;
var f5 = document.kirtleys.foldingchair.value * 0.1;
var g5 = document.kirtleys.bbq.value * 1;
var h5 = document.kirtleys.fridgefreezer.value * 1;
var a6 = document.kirtleys.gardentools.value * 0.1;
var j5 = document.kirtleys.dogcatkennel.value * 0.9;
var k5 = document.kirtleys.stepladder.value * 0.15;
var l5 = document.kirtleys.lawnmower.value * 0.3;
var m5 = document.kirtleys.potplantlarge.value * 0.2;
var n5 = document.kirtleys.potplantsmall.value * 0.1;
var o5 = document.kirtleys.sunlounger.value * 0.2;
var p5 = document.kirtleys.childsbikesetc.value * 0.2;
var q5 = document.kirtleys.whippersnipper.value * 0.15;
var r5 = document.kirtleys.workbench.value * 1.2;
var s5 = document.kirtleys.toolbox.value * 1;
var t5 = document.kirtleys.swing.value * 1;
var u5 = document.kirtleys.trampoline.value * 1;
var v5 = document.kirtleys.wheelbarrow.value * 0.48;



cuft = (aa+bb+cc+dd+ee+ff+gg+hh+a1+jj+kk+ll+mm+nn+oo+pp+qq+rr+ss+tt+uu+vv+ww+xx+yy+zz+b1+cc1+d1+e1+f1+g1+h1+b2+j1+k1+l1+m1+n1+o1+p1+q1+r1+s1+t1+u1+v1+w1+x1+y1+z1+a2+c2+d2+e2+f2+g2+h2+c1+j2+k2+l2+m2+n2+o2+p2+q2+r2+s2+t2+u2+v2+w2+x2+y2+z2);
cuft = cuft+(a3+b3+c3+d3+e3+f3+g3+h3+aa4+j3+k3+l3+m3+n3+o3+p3+q3+r3+s3+t3+u3+v3+w3+x3+y3+z3+a4+b4+c4+d4+e4+f4+g4+h4+a5+j4+k4+l4+m4+n4+o4+p4+q4+r4+s4+t4+u4+v4+w4+x4+y4+z4+b5+c5+d5+e5+f5+g5+h5+a6+j5+k5+l5+m5+n5+o5+p5+q5+r5+s5+t5+u5+v5);

tot = +cuft.toFixed(2);

var rslt = "" + tot.toString() + " CBM (Approx)";
document.kirtleys.answer.value = rslt
}
//  End -->
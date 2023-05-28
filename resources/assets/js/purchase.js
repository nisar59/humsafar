$(document).ready(function() {

	$(document).on('input','.price',function () {
	calculate_total();
	});

	$(document).on('input','.quantity',function () {
	calculate_total();
	});



	$(document).on('click','.add-subunits', function() {
		var path=$(this).data('link');
		var prnt=$(this).parent().parent();
		$.ajax({
			url:path,
			success:function(data) {
				console.log(data);
				prnt.append(data);
			}
		});
	});


	$(document).on('change','.discount_type', function(){
		calculate_discount();
	});
	$(document).on('input','.discount_value', function(){
		calculate_discount();
	});


	$(document).on('input','.shipping_charges', function(){
		calculate_discount();
	});

	$(document).on('input','.payment_amount', function(){
		var purchs_total=parseInt($(".purchase_total").val());
		var paymt_amt=parseInt($(this).val());
		var paymt_due=purchs_total-paymt_amt;
		$(".payment_due").val(Twofloating(paymt_due));
	});


	$(document).on('click',".removepro",function() {
		var lnk=$(this).data('href');
		var ele=$(this);
		$.ajax({
			url:lnk,
			type:"GET",
			success:function(data) {
				console.log(data=='1');
				if(data=='1'){
					ele.parent().parent().remove();
					calculate_total();
				}
				else{
				alert('something went wrong');
				}
			},
			error:function() {
				alert('something went wrong');
			}
		});

	});


	$(document).on('click','.removesubunit',function() {
		var lnk=$(this).data('href');
		var ele=$(this);
		$.ajax({
			url:lnk,
			type:"GET",
			success:function(data) {
				console.log(data=='1');
				if(data=='1'){
					ele.parent().parent().remove();
				}
				else{
				alert('something went wrong');
				}
			},
			error:function() {
				alert('something went wrong');
			}
		});

	});



});

function calculate_total() {
	var qty=$(".quantity");
	var itm=0;
	var ntl=0;
	qty.each(function(){
		var tr=$(this).parent().parent();
		var qty=$(this).val();
		var prc=tr.find('.price').val();
		var ntprc=prc*qty;
		tr.find('.pronet').val(Twofloating(ntprc));

		itm+=parseInt(qty);
		ntl+=parseInt(ntprc);
	});

	$(".total_items").val(Twofloating(itm));
	$(".gross_total").val(Twofloating(ntl));
	calculate_discount();

}


function calculate_discount() {
	var dis_type=$(".discount_type").val();
	var dis_vlu=parseInt($(".discount_value").val());
	var net_dis=0;
	var purchs_total=0;
	var gross_total=parseInt($(".gross_total").val());
	var shipping_charges=parseInt($(".shipping_charges").val());

	if(dis_type=='fixed'){
		net_dis=dis_vlu;
	}
	else{
		net_dis=(gross_total*dis_vlu)/100;
	}
	purchs_total=(gross_total+shipping_charges)-net_dis;

	$(".net_discount").val(Twofloating(net_dis));
	$(".purchase_total").val(Twofloating(purchs_total));
	$(".payment_amount").val(Twofloating(purchs_total));
	
}


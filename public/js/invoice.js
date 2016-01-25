function print_today() {
 
  var now = new Date();
  var months = new Array('January','February','March','April','May','June','July','August','September','October','November','December');
  var date = ((now.getDate()<10) ? "0" : "")+ now.getDate();
  function fourdigits(number) {
    return (number < 1000) ? number + 1900 : number;
  }
  var today =  months[now.getMonth()] + " " + date + ", " + (fourdigits(now.getYear()));
  return today;
}

// from http://www.mediacollege.com/internet/javascript/number/round.html
function roundNumber(number,decimals) {
  var newString;// The new rounded number
  decimals = Number(decimals);
  if (decimals < 1) {
    newString = (Math.round(number)).toString();
  } else {
    var numString = number.toString();
    if (numString.lastIndexOf(".") == -1) {// If there is no decimal point
      numString += ".";// give it one at the end
    }
    var cutoff = numString.lastIndexOf(".") + decimals;// The point at which to truncate the number
    var d1 = Number(numString.substring(cutoff,cutoff+1));// The value of the last decimal place that we'll end up with
    var d2 = Number(numString.substring(cutoff+1,cutoff+2));// The next decimal, after the last one we want
    if (d2 >= 5) {// Do we need to round up at all? If not, the string will just be truncated
      if (d1 == 9 && cutoff > 0) {// If the last digit is 9, find a new cutoff point
        while (cutoff > 0 && (d1 == 9 || isNaN(d1))) {
          if (d1 != ".") {
            cutoff -= 1;
            d1 = Number(numString.substring(cutoff,cutoff+1));
          } else {
            cutoff -= 1;
          }
        }
      }
      d1 += 1;
    } 
    if (d1 == 10) {
      numString = numString.substring(0, numString.lastIndexOf("."));
      var roundedNum = Number(numString) + 1;
      newString = roundedNum.toString() + '.';
    } else {
      newString = numString.substring(0,cutoff) + d1.toString();
    }
  }
  if (newString.lastIndexOf(".") == -1) {// Do this again, to the new string
    newString += ".";
  }
  var decs = (newString.substring(newString.lastIndexOf(".")+1)).length;
  for(var i=0;i<decimals-decs;i++) newString += "0";
  //var newNumber = Number(newString);// make it a number if you like
  return newString; // Output the result to the form field (change for your purposes)
}

function update_total() {
  var total = 0;
  $('.price').each(function(i){
    price = $(this).html().replace("$","");
    if (!isNaN(price)) total += Number(price);
  });

  total = roundNumber(total,2);

  $('#subtotal').html("$"+total);
  $('#total').html("$"+total);
  
  update_balance();
}

function update_balance() {
  var due = $("#total").html().replace("$","") - $("#paid").val().replace("$","");
  due = roundNumber(due,2);
  
  $('.due').html("$"+due);
}

function update_price() {
  var row = $(this).parents('.item-row');

  //console.log(row.find('.item-name textarea').val()+':'+ row.find('.cost').val() );

  var price = row.find('.cost').val().replace("$","") * row.find('.qty').val();
  var tax1_percent = ((row.find('.tax1_percent').val().replace("%","") / 100) + 1); // * row.find('.qty').val()

  //console.log(row.find('.item-name textarea').val()+':'+ price);

  tax1_percent = roundNumber(tax1_percent,2);
  price = roundNumber(price,2);

  price = roundNumber(tax1_percent * price,2);
  isNaN(price) ? row.find('.price').html("N/A") : row.find('.price').html("$"+price);

  //console.log(row.find('.item-name textarea').val()+':'+price);

  update_total();
}

function bind() {
  $(".cost").blur(update_price);
  $(".qty").blur(update_price);
  $(".tax1_percent").blur(update_price);
}

$(document).ready(function() {

  $('input').click(function(){
    $(this).select();
  });

  $("#paid").blur(update_balance);
   
  $("#addrow").click(function(){
    $(".item-row:last").after('<tr class="item-row"><td class="item-name"><div class="delete-wpr"><textarea>Item Name</textarea><a class="delete" href="javascript:;" title="Remove row">X</a></div></td><td class="description"><textarea>Description</textarea></td><td><textarea class="cost">$0</textarea></td><td><textarea class="qty">0</textarea></td><td><span class="price">$0</span></td></tr>');
    //if ($(".delete").length > 0) $(".delete").show();
    bind();
  });
  
  bind();
  /*
  $(".delete").on('click',function(){
    $(this).parents('.item-row').remove();
    update_total();
    if ($(".delete").length < 2) $(".delete").hide();
  });*/
  
	$(document).on({
	    click: function () {
		    $(this).parents('.item-row').remove();
		    update_total();
		    if ($(".delete").length < 2) $(".delete").hide();
	    }
	}, ".delete");
  
	$(document).on({
	    mouseenter: function () {
			$('.delete').hide(); $(this).find('.delete').show();
	        //stuff to do on mouse enter
	    },
	    mouseleave: function () {
	        //stuff to do on mouse leave
	    }
	}, ".item-row");
	
  /*$("#cancel-logo").click(function(){
    $("#logo").removeClass('edit');
  });
  $("#delete-logo").click(function(){
    $("#logo").remove();
  });
  $("#change-logo").click(function(){
    $("#logo").addClass('edit');
    $("#imageloc").val($("#image").attr('src'));
    $("#image").select();
  });
  $("#save-logo").click(function(){
    $("#image").attr('src',$("#imageloc").val());
    $("#logo").removeClass('edit');
  });*/
  
  $("#date").val(print_today());

  $(".tax1_percent").blur();
});
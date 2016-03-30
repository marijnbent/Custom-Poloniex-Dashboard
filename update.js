var oldData = "Not initialized";
var oldTotalWorth = "Not initialized";
var updated = 0;

$(window).load(function () {
    console.log('Loaded.');

    $('[data-toggle="tooltip"]').tooltip();

    ajax();
});

function ajax() {
    $.ajax({
        url: "response.php",
        success: function (data) {
            updateData(data);
        }
    });
}

function updateData(data) {
    console.log('Data incoming');
    console.log(data);

    var extraInfo = data['extraData'];
    var coinsData = data['coinsData'];

    var trHTML = "";
    var profit;
    var profitStatus;
    var status = "";
    var priceStatus = "";

    $.each(coinsData, function (i, item) {
        if (oldData != "Not initialized") {
            if (parseFloat(item.price) < parseFloat(oldData[i].price)) {
                //console.log('Price went down');
                status = "danger";

            } else if (parseFloat(item.price) > parseFloat(oldData[i].price)) {
                //console.log('Price went up');
                status = "success";
            } else {
                status = "";
            }
        }
        if (item.price > item.paidPC.toFixed(8)) {
            priceStatus = "green";
        } else if (item.price < item.paidPC.toFixed(8)) {
            priceStatus = "darkred";
        } else {
            priceStatus = "";
        }

        profit = item.worth.toFixed(8) - item.pricePaidWorth.toFixed(8);
        if (profit >= 0) {
            profitStatus = "success";
        } else {
            profitStatus = "danger";

        }

        trHTML += '<tr class="data ' + status + '"><td>' + item.coin + '</td><td style="color: ' + priceStatus + '">' + item.price + '</td><td>' + item.paidPC.toFixed(8) + '</td><td align="right">' + parseFloat(item.amount).toFixed(2) + '</td><td></td><td class="tooltipWorth" title="' + item.pricePaidWorth.toFixed(8) + '" >' + item.worth.toFixed(8) + ' &nbsp;&nbsp;<span class="label label-'+profitStatus+'">'+profit.toFixed(8)+'</span> </td></tr>';


    });


    $(".table tr.data").remove();
    $('.table').append(trHTML);

    Tipped.create('.tooltipWorth', {
        position: 'left',
        close: true

    });

    updated++;
    document.getElementById('updated').innerHTML = updated + ' times updated';


    var priceStatus = "";
    if (oldTotalWorth != "Not initialized") {
        if (parseFloat(extraInfo['totalWorth']) < parseFloat(oldTotalWorth)) {
            priceStatus = "darkred";
        } else if (parseFloat(extraInfo['totalWorth']) > parseFloat(oldTotalWorth)) {
            priceStatus = "green";
        }
        document.getElementById('totalWorth').style.color = priceStatus;
    }
    document.getElementById('totalWorth').innerHTML = extraInfo['totalWorth'].toFixed(8);
    document.getElementById('amountDeposited').innerHTML = extraInfo['amountDeposited'].toFixed(8);


    oldData = coinsData;

    if (oldTotalWorth == "Not initialized") {
        oldTotalWorth = extraInfo['totalWorth'];
    }

    window.setTimeout(ajax, 3000);
}


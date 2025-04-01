export class Report {

    quantitySelect(value, data) {

        let saleQuantity = parseFloat(value)
        let purchaseQuantity = parseFloat(data.pivot.quantity)
        let existingQuantity, total = 0.0

        if (saleQuantity >= purchaseQuantity) {
            total = parseFloat(data.pivot.subtotal)
            existingQuantity = purchaseQuantity
        } else {
            total = (parseFloat(data.pivot.subtotal) / purchaseQuantity) * saleQuantity
            existingQuantity = saleQuantity
        }

        return [total, existingQuantity]
    }

    calculateFifo(saleQuantity, purchaseList) {

        var total = 0.0
        var quantity = 0.0
        if (purchaseList) {
            if(saleQuantity >= quantity) {
                _.sortBy(purchaseList, 'purchase_date').map(data => {
                        let salesQuantityCount = saleQuantity - quantity
                        let dataInfo = this.quantitySelect(salesQuantityCount, data)
                        total += dataInfo[0]
                        quantity += dataInfo[1]
                });
            }

        }
        return total ? total : 0;
    }

    calculateLifo(saleQuantity, purchaseList) {

        var total = 0.0
        var quantity = 0.0
        if (purchaseList) {
            if(saleQuantity >= quantity) {
                _.sortBy(purchaseList, 'purchase_date').reverse().map(data => {
                    let salesQuantityCount = saleQuantity - quantity;
                    let dataInfo = this.quantitySelect(salesQuantityCount, data)
                    total += dataInfo[0]
                    quantity += dataInfo[1]
                });
            }

        }
        return total ? total : 0;
    }


    checkPaymentStatus(total, payments, withAccounting = null) {
        let totalPaid = 0.0
        total = parseFloat(total)
        for (let i = 0; i < payments.length; i++) {
            totalPaid += parseFloat(payments[i].paid)
        }
        totalPaid = totalPaid - total
        if (withAccounting) {
            return totalPaid < 0 ? 'cr' + parseFloat(totalPaid).toFixed(2) : 'dr ' + parseFloat(totalPaid).toFixed(2)
        } else {
            return totalPaid < 0 ? parseFloat(totalPaid).toFixed(2) : parseFloat(totalPaid).toFixed(2)
        }
    }

    purchaseTotalPaidTotal(data, totalAmount=0, totalPaid=0) {
        data.map(data => {
            totalAmount += parseFloat(data.total)
            return data.payments
        }).map(payments => {
            for (let i = 0; i < payments.length; i++) {
                totalPaid += parseFloat(payments[i].paid)
            }
        })
        return {totalAmount, totalPaid};
    }

    giveMeLastOne(value, items) {
        let total = []
        let val = _.last(value)
        if (val) {
            for (let i = 0; i < items.length; i++) {
                total[i] = val[items[i]];
            }
            return total;
        }
        else {
            return null
        }
    }

    totalCountFromObjectPivot(value, item) {
        let total = 0.0
        if (value) {
            for (let i = 0; i < value.length; i++) {
                total = total + parseFloat(value[i].pivot[item]);
            }
            return total;
        } else return 0;

    }

    getTotalMeasurementProfit(product, sales, item) {
        let totalMeasurementProfit = 0

        sales ? sales.map(data => {
            let measurement = product['measurement']
            if (measurement == null || measurement == 0) {
                return 0
            }
            let saleQuantity = data.pivot[item]
            let purchaseQuantity = (saleQuantity * Math.pow(measurement, 2) / Math.pow(30.48, 2));
            let measurementProfit = ((product['buying_price'] / purchaseQuantity) - (product['buying_price'] / saleQuantity)) * saleQuantity
            totalMeasurementProfit += measurementProfit
        }) : ''

        return totalMeasurementProfit;
    }


    settings() {
        return JSON.parse(document.head.querySelector('meta[name="settings"]').getAttribute('content'))
    }


}
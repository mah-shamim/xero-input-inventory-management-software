export class SalePurchaseMethods {

    initialPreviewSetup(apps) {
        var today= moment(new Date()).format('YYYY-MM-DD')
        let saleCustomer= apps.customers.filter(data=> data.id==apps.forms.customer_id)[0]
        apps.salePrintData=
            {
                sale:{
                    customer:{
                        phone:saleCustomer.phone,
                        created_at:saleCustomer.created_at,
                        address:saleCustomer.address,
                        user:{
                            name:saleCustomer.user.name
                        }
                    },
                    ref:'Preview',
                    sales_date:today,
                    created_at:today,
                    products:[],
                    payments:[{payment_type:apps.payment_type,created_at:today,paid:apps.forms.paid?parseInt(apps.forms.paid):0}],
                    overall_discount:apps.forms.overall_discount?apps.forms.overall_discount:0,
                    shipping_cost:apps.forms.shipping_cost?apps.forms.shipping_cost:0,
                    total:apps.total
                },
                returns:[],
                company:{
                    name:auth.user.company.name,
                    address1:auth.user.company.address1,
                    address2:auth.user.company.address2,
                    contact_phone:auth.user.company.contact_phone,
                },
            }

        for(let i=0; i<apps.items.length; i++){
            let item = apps.items[i]
            let quantityStr = item.quantity +' ' +item.units.filter(e=>e.id==item.unit)[0]['key']
            apps.salePrintData.sale.products.push(
                {code:'Preview', name:item.pname,quantityStr:quantityStr,status:'1',
                    pivot:{price:item.unit_price,discount:item.discount?item.discount:0,
                        subtotal:((parseInt(item.unit_price)*parseInt(item.quantity?item.quantity:0))-
                            parseInt(item.discount?item.discount:0)).toFixed(4)
                }}
            )
        }
        setTimeout(function () {
            document.getElementById('printButton').click()

        },300)
    }
}
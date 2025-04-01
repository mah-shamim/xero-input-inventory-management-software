const warehouseType = [
    {
        text: 'Storage',
        value: 'storage'
    },
    {
        text: 'Picking',
        value: 'picking'
    },
]

const warehousePickingType = [
    {
        text: 'Fixed',
        value: 'fixed'
    },
    {
        text: 'Mobile',
        value: 'mobile'
    },
]

const warehouseStorageType = [
    {
        text: 'Storage',
        value: 'storage'
    },
    {
        text: 'Staging',
        value: 'staging'
    },
    {
        text: 'Both',
        value: 'both'
    }
]

const accountingMethod = [
    {
        text:'Average',
        value:'avg'
    },
    {
        text:'FiFo',
        value:'fifo'
    }
]

const paymentMethods= [
    {
        id: 1,
        name: 'Cash'
    },
    {
        id: 2,
        name: 'Credit Card'
    },
    {
        id: 3,
        name: 'Cheque'
    },
]

export {
    warehouseType,
    warehousePickingType,
    warehouseStorageType,
    accountingMethod,
    paymentMethods
}
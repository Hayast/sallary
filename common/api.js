const { http } = uni.$u
// post请求，获取菜单
//export const postMenu = (params, config = {}) => http.post('/api/ebapi/public_api/index', params, config)
// get请求，获取菜单，注意：get请求的配置等，都在第二个参数中，详见前面解释
//export const getMenu = (data) => http.get('/api/ebapi/public_api/index', data)

/**
 * 
 * @param {*} data 
 * Nodejs版本...
 */

//工序列表
export const getBarcode = (data) => http.get('/api/ticket/barcode', data)
export const saveBarcode = (data) => http.post('/api/ticket/barcode/save', data)
export const saveBarcodeLists = (data) => http.post('/api/ticket/barcode/saveLists', data)
//扫码后工序工菲
export const getAppSku = (data) => http.get('/api/ticket/appSku', data)
//后台批量
export const getBarcodeSku = (data) => http.get('/api/ticket/sku', data)

//进度
export const getProgress = (data) => http.get('/api/ticket/progress', data)

//参数
export const getParameter = (data) => http.get('/api/parameter', data)
export const saveParameter = (data) => http.post('/api/parameter/save', data)
export const dbInitParameter = (data) => http.post('/api/parameter/dbInit', data)
export const saveParameterLists = (data) => http.post('/api/parameter/saveLists', data)

//帐号
export const astCf = (data) => http.post('/api/astCf', data)
//小程序取得信息
export const astMp = (data) => http.post('/api/astMp', data)

//计件记录
export const saveOrder = (data) => http.post('/api/ticket/order/save', data)
export const saveOrderLists = (data) => http.post('/api/ticket/order/saveLists', data)
export const saveAppOrder = (data) => http.post('/api/ticket/saveAppOrder', data)
export const getOrder = (data) => http.get('/api/ticket/order', data)

//工资
export const saveSallary = (data) => http.post('/api/ticket/sallary/save', data)
export const saveSallaryLists = (data) => http.post('/api/ticket/sallary/saveLists', data)
export const createSallary = (data) => http.post('/api/ticket/sallary/create', data)
export const getSallary = (data) => http.get('/api/ticket/sallary', data)

//工序工价
export const copyTicket = (data) => http.post('/api/ticket/ticket/copy', data)
export const saveTicket = (data) => http.post('/api/ticket/ticket/save', data)
export const saveTicketLists = (data) => http.post('/api/ticket/ticket/saveLists', data)
export const getTicket = (data) => http.get('/api/ticket/ticket', data)
export const updateQRCode = (data) => http.get('/api/ticket/updateQRCode', data)
export const postQRCode = (data) => http.post('/api/ticket/postQRCode', data)

//物料
export const saveMaterial = (data) => http.post('/api/store/material/save', data)
export const saveMaterialLists = (data) => http.post('/api/store/material/saveLists', data)
export const getMaterial = (data) => http.get('/api/store/material', data)

export const saveMio = (data) => http.post('/api/store/mio/save', data)
export const saveMioLists = (data) => http.post('/api/store/mio/saveLists', data)
export const getMio = (data) => http.get('/api/store/mio', data)

//产品
export const savePio = (data) => http.post('/api/store/pio/save', data)
export const savePioLists = (data) => http.post('/api/store/pio/saveLists', data)
export const getPio = (data) => http.get('/api/store/pio', data)
export const savePin = (data) => http.post('/api/store/pio/savePin', data)
export const getSpi = (data) => http.get('/api/store/pio/getSpi', data)
export const addSpi = (data) => http.post('/api/store/pio/addSpi', data)
export const saveSpi = (data) => http.post('/api/store/pio/saveSpi', data)
export const saveCartOrder = (data) => http.post('/api/store/pio/saveCartOrder', data)

export const getExpress = (data) => http.get('/api/store/express', data)
export const saveExpress = (data) => http.post('/api/store/express/save', data)

export const getWh = (data) => http.get('/api/store/express', data)
export const getPt = (data) => http.get('/api/store/position', data)
export const getWhio = (data) => http.get('/api/store/wbio', data)
export const saveWhio = (data) => http.post('/api/store/wbio/save', data)

export const saveProduct = (data) => http.post('/api/store/product/save', data)
export const saveProductLists = (data) => http.post('/api/store/product/saveLists', data)
export const getProduct = (data) => http.get('/api/store/product', data)
export const getProductItem = (data) => http.get('/api/store/product/item', data)

//销售订单
export const saveSaleOrder = (data) => http.post('/api/sale/order/save', data)
export const saveSaleOrderLists = (data) => http.post('/api/sale/order/saveLists', data)
export const getSaleOrder = (data) => http.get('/api/sale/order', data)
//样品
export const saveModules = (data) => http.post('/api/store/modules/save', data)
export const saveModulesLists = (data) => http.post('/api/store/modules/saveLists', data)
export const getModules = (data) => http.get('/api/store/modules', data)

export const uploadApi = (data) => http.post('/api/upload', data)

// 上次启动时的信息
let lifeData = {};

try {
	// 尝试获取本地是否存在lifeData变量，第一次启动APP时是不存在的
	lifeData = uni.getStorageSync('lifeData');
} catch (e) {

}

//变量尽量用字符或对象

// 需要永久存储，且下次APP启动需要取出的，在state中的变量名
let saveStateKeys = ['Domain','Company', 'TUserId1', 'ScanType', 'CreateType',
	'DefaultUser', 'DefaultSkus', 'DefaultBlePrinter',
	'PageSize', 'PrinterType','CurrProdType','ProductType', 'Cart','HideFinish','MyQrcode','SaveLastPost','ShowMyDept','UrlLists'
];

// 保存变量到本地存储中
const saveLifeData = function(key, value) {
	// 判断变量名是否在需要存储的数组中
	if (saveStateKeys.indexOf(key) != -1) {
		// 获取本地存储的lifeData对象，将变量添加到对象中
		let tmp = uni.getStorageSync('lifeData');
		// 第一次打开APP，不存在lifeData变量，故放一个{}空对象
		tmp = tmp ? tmp : {};
		tmp[key] = value;
		// 执行这一步后，所有需要存储的变量，都挂载在本地的lifeData对象中
		uni.setStorageSync('lifeData', tmp);
	}
}

let state = {
		// 如果上面从本地获取的lifeData对象下有对应的属性，就赋值给state中对应的变量  加上前缀，是防止变量名冲突，也让人一目了然
		
		Company: lifeData.Company ? lifeData.Company : {},
		TUserId1: lifeData.TUserId1 ? lifeData.TUserId1 : {
			name: '13580704353'
		},
		DefaultBlePrinter: lifeData.DefaultBlePrinter ? lifeData.DefaultBlePrinter : [{
			name: '',
			deviceId: '',
			services: [],
			RSSI: ''
		}],
		ProductType: lifeData.ProductType ? lifeData.ProductType : [],
		CurrProdType: lifeData.CurrProdType ? lifeData.CurrProdType : -1,
		PrinterType: lifeData.PrinterType ? lifeData.PrinterType : 1,
		PageSize: lifeData.PageSize ? lifeData.PageSize : 20,
		ScanType: lifeData.ScanType ? lifeData.ScanType : 0,
		CreateType: lifeData.CreateType ? lifeData.CreateType : 0,
		HideFinish: lifeData.HideFinish ? lifeData.HideFinish : false,
		MyQrcode: lifeData.MyQrcode ? lifeData.MyQrcode : false,
		SaveLastPost: lifeData.SaveLastPost ? lifeData.SaveLastPost : false,
		ShowMyDept: lifeData.ShowMyDept ? lifeData.ShowMyDept : false,
		PlayAudio: lifeData.PlayAudio ? lifeData.PlayAudio : true,
		UrlLists: lifeData.UrlLists ? lifeData.UrlLists : [{name:'首页',url:'/pages/index/index',photo:'home'},{name:'扫码',url:'/pages/ticket/scan',photo:'home'},{name:'记录',url:'/pages/ticket/myscan',photo:'photo'},{name:'我的',url:'/pagesUcenter/ucenter',photo:'account'}],
		
		Cart: lifeData.Cart ? lifeData.Cart : [],
		
		DefaultSkus: lifeData.DefaultSkus ? lifeData.DefaultSkus : [],
		DefaultUser: lifeData.DefaultUser ? lifeData.DefaultUser : {uid:'',nickname:''},
		
		Domain: lifeData.Domain ? lifeData.Domain : 'https://n1.anxinjijian.cn',
		//Domain: lifeData.Domain ? lifeData.Domain : 'https://n1.yunitapp.com',
		
		//不同小程序 修改AppId manifest.json里的微信小程序APPID 发布的APPID
		AppId: 'wx372a2a6dcc7eaf4d', //it-05@yunitapp.com 扫码记数
		//AppId: 'wx0128fc9cf32423c3', //it-04@yunitapp.com 通易网络计件记账
		//AppId: 'wxe6f4d3a639315b2d', //wx02@yunitapp.com 扫码计时计件
		//AppId: 'wx6bf9a5e3dd5613bc', //jijian1@yunitapp.com 扫码计件计时结算
		Version: '1.0.1',
		MpShareTitle:'扫码记数',
		MpSharePath:'/pages/share',
		IsSearching: false,
		CurrPrinterDev: [],
		CurrPrinterInfo: {isConnected:false},
		BleDevice: [],
		ScanTypeLists: [{
				name: '扫码选择工序',
				value: 0,
				checked: false
			},
			{
				name: '扫码完成自动提交默认工序',
				value: 1,
				checked: false
			},
			{
				name: '人工选择工单',
				value: 2,
				checked: false
			},
			{
				name: '裁床号批量操作',
				value: 3,
				checked: false
			},
			{
				name: '扫工序码',
				value: 4,
				checked: false
			},
			{
				name: '扫码计次',
				value: 5,
				checked: false
			}
		]
		

	},
	getters = {
		//globalInfo:'global/get'
		get(state) {
			return state;
		}
	},
	mutations = {
		set(state, payload) {
			// 判断是否多层级调用，state中为对象存在的情况，诸如user.info.score = 1
			//setGlobalInfo: 'global/set'  this.setGlobalInfo({name:'Domain',value:val})
			let nameArr =JSON.parse(JSON.stringify( payload.name.split('.')));
			let saveKey = '';
			let len = nameArr.length;
			if (len >= 2) {
				let obj = state[nameArr[0]];
				for (let i = 1; i < len - 1; i++) {
					obj = obj[nameArr[i]];
				}
				obj[nameArr[len - 1]] = payload.value;
				saveKey = nameArr[0];
			} else {
				// 单层级变量，在state就是一个普通变量的情况
				state[payload.name] = payload.value;
				saveKey = payload.name;
			}
			// 保存变量到本地，见顶部函数定义
			saveLifeData(saveKey, state[saveKey])
		}
	},
	actions = {

	}
export default {
	namespaced: true,
	state,
	getters,
	mutations,
	actions
}
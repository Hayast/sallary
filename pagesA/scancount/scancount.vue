<template>
	<view class="container">
		<view class=" bg-purple-light ">
			<u-row >
				<u-col :span="12" stop>
					<view class="demo-layout u-font-18">
						扫码计次服务端
					</view>
				</u-col>
			</u-row>
		</view>
		<view class="container u-font-18" style="margin:15rpx;">
			<view class="qrimg">
				<view class="qrimg-i">
					<tki-qrcode v-if="ifShow" cid="qrcode2" ref="qrcode" :val="val" :size="600" :onval="onval"
						:loadMake="loadMake" :usingComponents="true" @result="qrR" />
				</view>
			</view>

			<view class="u-text-center u-font-18">
				{{userInfo.dept}} - {{userInfo.nickname}}
			</view>
			<view class="u-text-center u-font-18">
				{{TDtNow}}
			</view>
		</view>

		<view class="u-font-14 u-type-success" v-show="ResMsg.ShowSuccess">
			{{ResMsg.MsgSuccess}}
		</view>
		<view class="u-button-area">
			<u-row>
				<u-col :span="12" stop>
					<view class="demo-layout bg-purple-dark">
						<u-button @click="start" type="primary" :custom-style="butStyle">{{TStatusText[TStatus]}}
						</u-button>
					</view>
				</u-col>
			</u-row>
		</view>
		<view class="u-font-14 u-type-error" v-show="ResMsg.ShowError">
			{{ResMsg.MsgError}}
		</view>
		<view class="u-font-14">
			{{ResMsg.MsgRemark}}
		</view>
	</view>
</template>

<script>
	import tkiQrcode from '@/pagesA/components/tki-qrcode/tki-qrcode.vue'
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		updateQRCode,
		astCf,
		getParameter
	} from '@/common/api.js';
	export default {
		data() {
			return {
				TSleep: 20,
				qrcodeParams:{dept:0,company_no:''},
				qrcodeText:'000000',
				border:false,
				labelPosition:'left',
				ifShow: true,
				val: '二维码', // 要生成的二维码值
				size: 300, // 二维码大小
				cpSize: 300,
				unit: 'upx', // 单位
				background: '#b4e9e2', // 背景色
				foreground: '#309286', // 前景色
				pdground: '#32dbc6', // 角标色
				icon: '', // 二维码图标
				iconsize: 40, // 二维码图标大小
				lv: 3, // 二维码容错级别 ， 一般不用设置，默认就行
				onval: false, // val值变化时自动重新生成二维码
				loadMake: true, // 组件加载完成后自动生成二维码
				src: '22222222222222222222222222222222', // 二维码生成后的图片地址或base64
				TShowQrcode: false,
				TDtNow: '',
				timer: '',
				value: 0,
				TQrText: 'Waiting',
				TWidth: 320,
				TMargin: 15,
				TStatus: 1,
				TStatusText: ['开 始', '暂 停'],
				TQuantity: '',
				deptLists: [],
				span: 4,
				offset: 0,
				justify: 'start',
				radioCheckWidth: 'auto',
				radioCheckWrap: true,
				labelPosition: 'left',
				ResMsg: {},
				ResMsgT: {
					ShowSuccess: false,
					ShowError: false,
					MsgSuccess: '',
					MsgError: '',
					MsgRemark: ''
				},
				butStyle: {
					marginTop: '20px', // 注意驼峰命名，并且值必须用引号包括，因为这是对象
					fontSize: '18px'
				},
				inputStyle: {
					marginTop: '10px',
					fontSize: '18px'
				},
				chkStyle: {
					margin: '10px',
					fontSize: '14px'
				},
				QrcodeStyle: {
					width: '354px',
					height: '354px'
				}
			}
		},
		components: {
			tkiQrcode
		},
		onLoad() {
			this.getDeptParameter();
			this.getQrcode();
			this.ResMsg = this.deepClone(this.ResMsgT);
			this.qrcodeParams.dept=this.userInfo.dept
			this.qrcodeParams.company_no=this.userInfo.company_no
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		mounted() {
			var that = this
			uni.getSystemInfo({
				success: function(res) {
					that.TWidth = res.windowWidth - 2 * parseInt(that.TMargin);
					that.size = res.windowWidth - 2 * parseInt(that.TMargin);
					that.QrcodeStyle.width = that.TWidth;
					that.QrcodeStyle.height = that.TWidth * 2 + 30;
				}
			})
			if (this.userInfo.TNum1)
				this.TSleep = this.userInfo.TNum1
			if (this.TSleep > 0)
				this.timer = setInterval(this.getQrcode, this.TSleep * 1000);

		},
		beforeDestroy() {
			clearInterval(this.timer);
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),
			
			async getQrcode() {
				let res=await updateQRCode({params:this.qrcodeParams});
				//qrcodeText
				console.log(res);
				if(res.code==0){
					if(this.qrcodeText!=res.qrcodeText){
						this.qrcodeText=res.qrcodeText;
						this.creatQrcode(this.qrcodeText);
					}
				}else{
					uni.showToast({
						title:'获取失败.'
					})
				}
				
				// if (this.TStatus == 1) {
				// 	this.value++;
				// 	this.TDtNow = this.now(1)
				// 	var STime = this.TDtNow
				// 	STime = STime.replace(/-/g, '')
				// 	STime = STime.replace(/ /g, '')
				// 	STime = STime.replace(/:/g, '')
				// 	var InfoT = this.userInfo.company_no + '|' + STime + '|' + this.userInfo.dept + '|' + this.TSleep;
				// 	var QrCode = this.StrListToNum(InfoT, 1);
				// 	this.creatQrcode(QrCode);
				// }
			},
			creatQrcode(QrCode) {
				if(QrCode!=null && QrCode!=undefined && QrCode !='')
					this.$refs.qrcode._makeCode(QrCode)
			},
			qrR(res) {
				this.src = res
			},
			clearQrcode() {
				this.$refs.qrcode._clearCode()
				this.val = ''
			},
			ifQrcode() {
				this.ifShow = !this.ifShow
			},
			start() {
				if (this.TStatus == 1) {
					this.TStatus = 0
				} else {
					this.TStatus = 1
					//this.post()
				}
			},
			async getDeptParameter(){
				let params={
					actType:0,
					type:1
				}
				let res=await getParameter({params});
				this.deptLists=res.dataLists;
			},
			createCode(codeLength = 2) {
				var code = '';
				var random = new Array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
				for (var i = 0; i < codeLength; i++) {
					//设置随机数范围,这设置为0 ~ 36
					var index = Math.floor(Math.random() * 9);
					code += random[index];
				}
				return code;
			},
			StrListToNum(StrList, Rtt = '0') {
				if (StrList == '')
					return '';
				var Rtn = '99';
				if (Rtt == '1') {
					Rtn = this.createCode();
					if (parseInt(Rtn) > 99)
						Rtn = '99';
				}
				var Res = Rtt + Rtn + '';
				//'' PHP '
				var CharLists =
					"WXnopABCghijklmQRSTU23456VqrsHIJKLvwxMNOPYZ[]^_`abcdDEFGfetuyz!''()*,-01789:;<=>@{|}~.?#%&/+";
				var StrListLen = StrList.length;
				var StrListLenT = StrListLen + '';
				while (StrListLenT.length < 3)
					StrListLenT = "0" + StrListLenT;
				Res = Res + StrListLenT + '';
				var i = 0;
				for (var j = 0; j < StrList.length; j++) {
					var ResT = 989;
					var c = StrList.substring(j, j + 1) + '';
					for (i = 0; i < 94; i++) {
						if (CharLists.substring(i, i + 1) == c) {
							if (Rtt == '1')
								ResT = i + parseInt(Rtn);
							else
								ResT = i;
							break;
						}
					}
					var SResT = ResT + '';
					while (SResT.length < 3)
						SResT = '0' + SResT + '';
					Res = Res + SResT + '';
				}

				return Res;
			},



		}
	}
</script>

<style>
	.container {
		display: flex;
		flex-direction: column;
		width: 100%;
	}


	.qrimg {
		display: flex;
		justify-content: center;
	}

	.qrimg-i {
		margin-right: 10px;
	}

	.qrcode_img {
		width: 100px;
		height: 100px;
		display: flex;
		justify-content: center;
		margin: 0 auto;
	}

	.image {
		width: 100px;
		height: 100px;
	}

	.canvas-hide {
		/* 1 */
		position: fixed;
		right: 100vw;
		bottom: 100vh;
		/* 2 */
		z-index: -9999;
		/* 3 */
		opacity: 0;
	}

	.demo-layout {
		height: 70rpx;
		border-radius: 8rpx;
		margin: 20rpx 0;
	}

	.checkbox-area {
		margin-left: 20rpx;
		margin-bottom: 20rpx;
	}

	.bg-purple {
		background: #d3dce6;
	}

	.bg-purple-light {
		background: #e5e9f2;
	}

	.bg-purple-dark {
		background: #99a9bf;
	}

	// H5中，电脑端文档演示时，可能会导致演示块挤出边界，特别处理。
	// 真实使用环境不会产生此问题
	/* #ifdef H5 */
	.u-demo-area /deep/ .u-row {
		display: flex;
		flex-wrap: wrap;
	}

	/* #endif */
</style>

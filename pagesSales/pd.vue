<template>
	<view class="container">
		<view>
			<u-toast ref="uToast" />
		</view>
		<view class="u-wrap">
			<u-row>
				<u-col span="10">
					<view class="item title-text">
						{{ItemRecord.TName||'品項名稱...'}}
					</view>
				</u-col>
				<u-col span="2">
					<view style="top:10px;" @tap="returnPage('/pagesSales/cart')">
						<u-icon name="shopping-cart" size="36" color="red" ></u-icon>
						<view class="num-box">
							 <u-badge numberType="overflow" :type="type" max="99" :value="cartNum"></u-badge>
						</view>
					</view>
					
				</u-col>
			</u-row>
		</view>
		<view class="div-css-center">
			<!-- 
				<u--image :src="globalInfo.Domain+'/'+ItemRecord.TPhoto" mode="widthFix" style="width:100%;">
			-->
			<u--image :src="globalInfo.Domain+'/'+ItemRecord.TPhoto" mode="widthFix" style="width:100%;">
				<template v-slot:loading>
					<u-loading-icon color="blue"></u-loading-icon>
				</template>
			</u--image>
		</view>
		
		<view class="div-css">
			<view>
				<view style="display: inline-flex;margin-top:10rpx;">
					<view  v-for="(color, index1) in colorList" :key="index1" @click="selectSku(0,color)" class="info-btn"
					 :style="color==selectedColor?'background-color: #FF8C00;':'background-color: #F2F2F2;'">
						{{color}}
					</view>
				</view>
			</view>
			<view>
				<view style="display: inline-flex;margin-top:10rpx;">
					<view  v-for="(size, index2) in sizeList" :key="index2" @click="selectSku(1,size)" class="info-btn"
					 :style="size==selectedSize?'background-color: #FF8C00;':'background-color: #F2F2F2;'">
						{{size}}
					</view>
				</view>
			</view>
			<view>
				单价:<text class="info-text">{{price}}</text>
			</view>
			<view>
				库存量:<text class="info-text">{{storeQty}}</text>
			</view>
			<view>
				<u--form>
					<view>
						<u-row>
								<u-col span="8">
									<u-form-item label="数量">
										<u-number-box v-model="quantity" @change="valChange"></u-number-box>
									</u-form-item>
								</u-col>
								<u-col span="4">
									<u-button @click="addToCart()" type="primary"> 加入购物车 </u-button>
								</u-col>							
						</u-row>	
					</view>
				</u--form>
			</view>
			<view class="top">
				<u-divider text="图文描述"></u-divider>
				 <view class="u-content" style="min-height: 360px;">
					<u-parse v-if="ItemRecord.TRemark" :content="ItemRecord.TRemark" :tagStyle="style"></u-parse>
				</view>
			</view>
			
		</view>
		<u-picker mode="region" ref="uPicker" v-model="show" @confirm="confirm" :default-region="defaultregion" />
	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		astCf,
		astMp,
		getProduct,
		getParameter
	} from '@/common/api.js';
	export default {
		data() {
			return {
				cartNum:0,
				labelWidth:90,
				price:0,
				storeQty:0,
				Msg: '',
				TRemark: '',
				selectedColor: '',
				selectedSize: '',
				SelectedSkuTGuid: '',
				SelectedTPrice: 0,
				SelectedTQuantity: 0,
				quantity: 1,
				ItemRecord: {},
				defaultRegion: ['廣東省', '東莞市', ''],
				inputPca: '',
				TIsUsed: false,
				EditType: 0,
				show: false,
				colorList:[],
				sizeList:[],
				selectedColor:'',
				selectedSize:''

			};
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				admin: 'user/admin',
				globalInfo: 'global/get'
			})
		},
		async onLoad(options) {
			let that = this
			//console.log(options.id)
			//that.getProductInfo(options.id)
			//this.ItemRecord = JSON.parse(options.data)
			
			// if(this.globalInfo.carts==undefined)
			// 	||[]=[]
			this.ItemRecord = JSON.parse(decodeURIComponent(options.data))
			let skuList=[]
			let colorList=[]
			let sizeList=[]
			this.SelectedSkuTGuid=this.ItemRecord.TGuid
			for(var i=0;i<this.ItemRecord.TSkuLists.length;i++){
				let sku=this.ItemRecord.TSkuLists[i];
				let foundColor=false;
				for(let j=0;j<colorList.length;j++){
					if(colorList[j]==sku.TColor)
						foundColor=true;
				}
				if (!foundColor){
					colorList.push(sku.TColor);
					if (this.selectedColor=='')
						this.selectedColor=sku.TColor;
				}
				let foundSize=false;
				for(let j=0;j<sizeList.length;j++){
					if(sizeList[j]==sku.TSize)
						foundSize=true;
				}
				if (!foundSize){
					sizeList.push(sku.TSize);
					if (this.selectedSize=='')
						this.selectedSize=sku.TSize;
				}				
			}
			this.colorList=colorList;
			this.sizeList=sizeList;

		},
		onShow(){
			if(this.globalInfo.carts)
				this.cartNum=this.globalInfo.carts.length || 0
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setUserAdmin: 'user/setAdmin',
				setGlobalInfo: 'global/set'
			}),
			selectSku(idx,val){
				if (idx==0)
					this.selectedColor=val
				if (idx==1)
					this.selectedSize=val
				
			},
			gotoHomePage() {
				uni.switchTab({
					url: '/pages/index/index'
				})
			},
			valChange(e) {
							console.log('当前值为: ' + e.value)
						},
			returnPage(url) {
				let pages = getCurrentPages(); // 当前页面        
				let beforePage = pages[pages.length - 3]; // 上上一页        
				//beforePage.$vm.reFresh = Math.random()//触发上一页监听器            
				uni.navigateBack({
				    delta: 1,//返回上上一级注意这里要设置为2
				    success:function(){
				        //beforePage.$vm.reFresh();
				        console.log("返回成功!");
				    }
				})
				
				uni.navigateTo({
					url
				})
			},
			setDefault(e) {
				this.TIsUsed = e.detail.value
			},
			showRegionPicker() {
				this.show = true;
			},
			confirm(e) {
				this.ItemRecord.TProvince = e.province.label
				this.ItemRecord.TCity = e.city.label
				this.ItemRecord.TStr1 = e.area.label
				this.inputPca = e.province.label + ' ' + e.city.label + ' ' + e.area.label;
			},
			async addToCart() {
				if ( this.SelectedSkuTGuid == '' ) {
					this.showMsg('請選擇產品.')
					return
				}
				var carts = this.globalInfo.carts||[]
				var bFound = false
				for (var i = 0; i < carts.length; i++) {
					if (carts[i].TGuid == this.SelectedSkuTGuid) {
						carts[i].quantity = parseFloat(carts[i].quantity) + parseFloat(this.quantity)
						bFound = true
					}
				}
				if (!bFound) {
					if (this.selectedColor == undefined || this.selectedColor == null)
						this.selectedColor = ''
					if (this.selectedSize == undefined || this.selectedSize == null)
						this.selectedSize = ''
					if (this.SelectedTPrice<=0)
						this.SelectedTPrice=1000
					carts.push({
						'TGuid': this.SelectedSkuTGuid,
						'TKeyGuid': this.ItemRecord.TGuid,
						'name': this.ItemRecord.TName,
						'img': this.ItemRecord.TPhoto,
						'quantity': this.quantity,
						'price': this.SelectedTPrice,
						'color': this.selectedColor,
						'size': this.selectedSize,
						'spec': this.selectedColor + this.selectedSize,
						'selected': false,
						'TRemark': this.TRemark
					})
				}
				this.setGlobalInfo({
					name: "carts",
					value: carts
				})
				this.cartNum=this.globalInfo.carts.length;
				this.showMsg('成功加入')
				//this.returnPage('')
				//this.returnPage('/pagesSales/cart')
			},
			async getProductInfo(id) {
				let that = this
				const res = await this.HttpRequest({
					url: '/wx/getProductInfo',
					data: {
						TUserNull: 'Hay',
						TPhone: 'App',
						TPassword: 'NoUserPwd',
						TType: 0,
						Id: id,
						TKeyword: ''
					}
				})
				that.ItemRecord = res.DataLists
				that.selectedColor = that.ItemRecord.selectedColor
				that.selectedSize = that.ItemRecord.selectedSize
				that.SelectedSkuTGuid = that.ItemRecord.SelectedSkuTGuid
				that.SelectedTPrice = that.ItemRecord.SelectedTPrice
				that.SelectedTQuantity = that.ItemRecord.SelectedTQuantity
			},
			showMsg(msg) {
				uni.showToast({
					title: msg,
					icon: 'none'
				});
			},
			async postSaveRecord() {
				let that = this
				if (that.TIsUsed == true)
					that.ItemRecord.TIsUsed = 1
				else
					that.ItemRecord.TIsUsed = 0
				const res = await this.HttpRequest({
					method: 'Post',
					url: '/wx/postAddresssWx',
					data: {
						Id: that.ItemRecord.Id,
						TType: that.EditType,
						record: that.ItemRecord,
					}
				})
				uni.navigateBack()

			},
			getSelectedInfo(idx) {
				if (this.ItemRecord.TSkuLists == undefined)
					return
				var IsFound = false
				for (var i = 0; i < this.ItemRecord.TSkuLists.length; i++) {
					if (this.ItemRecord.TSkuLists[i].TColor == this.selectedColor && this.ItemRecord.TSkuLists[i].TSize ==
						this.selectedSize) {
						IsFound = true
						this.SelectedSkuTGuid = this.ItemRecord.TSkuLists[i].TGuid
						if (this.ItemRecord.TSkuLists[i].TPrice > 0) {
							this.SelectedTPrice = this.ItemRecord.TSkuLists[i].TPrice
						}
						this.SelectedTQuantity = this.ItemRecord.TSkuLists[i].TQuantity
						this.selectedColor = this.ItemRecord.TSkuLists[i].TColor
						this.selectedSize = this.ItemRecord.TSkuLists[i].TSize
					}
				}
				if (IsFound == false) {
					this.SelectedSkuTGuid = ''
					this.SelectedTPrice = 0
					if (idx == 1 && this.selectedSize != '') {
						this.selectedColor = ''
					}
					if (idx == 2 && this.selectedColor != '') {
						this.selectedSize = ''
					}
				}
			}
		},
		watch: {
			selectedColor: {
				handler(newData, oldData) {
					this.getSelectedInfo(1)
				},
				immediate: true
			},
			selectedSize: {
				handler(newData, oldData) {
					this.getSelectedInfo(2)
				},
				immediate: true
			}
		}
	};
</script>

<style lang="scss" scoped>
	/deep/ .line {
		color: $u-light-color;
		font-size: 28rpx;
	}
	
	.num-box{
		position: relative;
		width: 15px; 
		height: 15px;
		top:-35px;
		left:25px;
		background-color: #909193;
		color:red;
		border-radius: 15px;
	}
	
	
	.div-css{
		padding: 10upx;	
	}
	
	.info-btn {
		 margin-right: 8rpx;
		 min-width:80rpx;
		 text-align: center;
		 padding: 3rpx;
		 border-radius: 5rpx;
		 border:  1rpx;
	}
	
	.info-text {
		margin-left: 15rpx;
		color:royalblue;
	}
	
	.div-css-center{
		padding: 10upx;
		display: flex;
		flex-direction: column;
		justify-content: center;
		align-items: center;
	}
	

	.wrap {
		background-color: #f2f2f2;

		.top {
			background-color: #ffffff;
			border-top: solid 2rpx $u-border-color;
			padding: 22rpx;

			.item {
				display: flex;
				font-size: 32rpx;
				line-height: 100rpx;
				align-items: center;
				border-bottom: solid 2rpx $u-border-color;

				.left {
					width: 180rpx;
				}

				input {
					text-align: left;
				}
			}

			.address {
				padding: 20rpx 0;

				textarea {
					// width: 100%;
					height: 150rpx;
					background-color: #f7f7f7;
					line-height: 60rpx;
					margin: 40rpx auto;
					padding: 20rpx;
				}
			}

			.site-clipboard {
				padding-right: 40rpx;

				textarea {
					// width: 100%;
					height: 150rpx;
					background-color: #f7f7f7;
					line-height: 60rpx;
					margin: 40rpx auto;
					padding: 20rpx;
				}

				.clipboard {
					display: flex;
					justify-content: center;
					align-items: center;
					font-size: 26rpx;
					color: $u-tips-color;
					height: 80rpx;

					.icon {
						margin-top: 6rpx;
						margin-left: 10rpx;
					}
				}
			}
		}

		.bottom {
			margin-top: 20rpx;
			padding: 40rpx;
			padding-right: 0;
			background-color: #ffffff;
			font-size: 28rpx;

			.tag {
				display: flex;

				.left {
					width: 160rpx;
				}

				.right {
					display: flex;
					flex-wrap: wrap;

					.tags {
						width: 140rpx;
						padding: 16rpx 8rpx;
						border: solid 2rpx $u-border-color;
						text-align: center;
						border-radius: 50rpx;
						margin: 0 10rpx 20rpx;
						display: flex;
						font-size: 28rpx;
						align-items: center;
						justify-content: center;
						color: $u-content-color;
						line-height: 1;
					}

					.plus {
						//padding: 10rpx 0;
					}
				}
			}

			.default {
				margin-top: 50rpx;
				display: flex;
				justify-content: space-between;
				border-bottom: solid 2rpx $u-border-color;
				line-height: 64rpx;

				.tips {
					font-size: 24rpx;
				}

				.right {}
			}
		}
	}


	#button_div {
		position: fixed;
		bottom: var(--window-bottom);
	}

	.navigation {
		display: flex;
		justify-content: center;
		margin-bottom: 10rpx;
		border: solid 2rpx #f2f2f2;
		background-color: #ffffff;
		padding: 16rpx 0;

		.left {
			display: flex;
			font-size: 20rpx;

			.item {
				margin: 0 30rpx;

				&.car {
					text-align: center;
					position: relative;

					.car-num {
						position: absolute;
						top: -10rpx;
						right: -10rpx;
					}
				}
			}
		}

		.right {
			display: flex;
			font-size: 28rpx;
			align-items: center;

			.btn {
				line-height: 66rpx;
				padding: 0 30rpx;
				border-radius: 36rpx;
				color: #ffffff;
			}

			.cart {
				background-color: #ed3f14;
				margin-right: 30rpx;
			}

			.buy {
				background-color: #ff7900;
			}
		}
	}
</style>

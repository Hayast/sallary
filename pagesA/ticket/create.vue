<template>
	<view class="container">
		<view class="header">
			<view class="row-l-r">
				<view class="title-text">裁床编菲</view>
				<view>
					<u-icon v-if="showPage==0" name="search" color="#2979ff" size="36" @click="showFilter=true"></u-icon>
					<u-icon v-if="showPage==1" name="close-circle" color="#2979ff" size="36" @click="showPage=0"></u-icon>
				</view>
			</view>
		</view>
		<view class="body">
			<u-modal :show="showBarcode" z-index="999999" title="工菲预览" :show-cancel-button="true"
				:show-confirm-button="true" @confirm="postSaveRecord(0)" confirm-text="建立工菲" ref="uModal"
				@cancel="showBarcode=false" :async-close="false" confirm-color="#0055ff">
				<scroll-view scroll-y style="min-height: 320px;max-height: 420px;width: 100%;">
					<view v-for="(item,index) in dataLists" :key="index">
						<u-row>
							<u-col :span="8">{{item.TName}}</u-col>
							<u-col :span="2">{{item.TQuantity}}</u-col>
							<u-col :span="2">{{item.TNum1}}</u-col>
						</u-row>
					</view>
					<view>
						<u-row>
							<u-col :span="6">
								工菲数:{{amtRow}}
							</u-col>
							<u-col :span="6">
								总数量:{{amtQty}}
							</u-col>
						</u-row>
					</view>
				</scroll-view>
			</u-modal>
			<u-modal :show="showMsg" z-index="999999" title="操作提示" :show-confirm-button="true" @confirm="showMsg=false"
				ref="uModal" :async-close="false" confirm-color="#FF6347"><strong
					style="padding: 80rpx;">{{ModalMsg}}</strong>
			</u-modal>
			<u-modal :show="showFilter" title="搜索条件" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelFilter" @confirm="getTicket()" ref="uModal"
				:async-close="false" cancel-text="重设" confirm-text="搜索">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<u-form ref="uForm">
						<u-form-item :labelWidth="80" class="u-form-item-area" label="关键词">
							<u-input confirm-type="search" v-model="keyword" @confirm="doSearch()" />
						</u-form-item>
					</u-form>
				</view>
			</u-modal>

			<u-modal :show="showDel" title="确认删除" :closeOnClickOverlay="true" :show-cancel-button="true"
				:show-confirm-button="true" @cancel="cancelDelete" @confirm="delColor()" ref="uModal"
				:async-close="false" cancel-text="取消" confirm-text="确认删除">
				<view style="padding-left: 25rpx;padding-right: 25rpx;">
					<text class="font-red">操作删除后记录无法恢复. </text>
				</view>
			</u-modal>
			<u-picker
				:show="showTicket"
				:columns="ticketLists"
				keyName="TName"
				ref="uPicker1"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
					
			<view class="wrap">
				<view class="u-bg-gray from-item">
					<u--form>
					<u-form-item :labelWidth="labelWidth" label="工序工价" borderBottom
						@click="showTicket=true; hideKeyboard()">
						<u--input v-model="selectedTicket.TName" disabled disabledColor="#ffffff" placeholder="请选择工序工价表"
							border="none"></u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item>
					</u--form>
				</view>
				<view>
					<u-toast ref="uToast"></u-toast>
					<u-tabs :list="tabLists" @change="changeCurrent"></u-tabs>
				</view>
				<view class="s-s-p-c-wrapper">
					<!--左边虚化-->
					<view class="h-c-box h-c-box-left"></view>
					<!--右边虚化-->
					<view class="h-c-box h-c-box-right"></view>
					<!--横向滚动-->
					<scroll-view class="s-s-p-c" scroll-x>
						<view class="s-s--s-w" v-for="(item, index) in colorNameLists" :key="index">
							<u-button @click="changeColor(index)" type="primary" size="mini">{{item.name}}</u-button>
						</view>
					</scroll-view>
				</view>
				<view v-if="current==1" class="s-s-p-c-wrapper">
					<view class="h-c-box h-c-box-left"></view>
					<view class="h-c-box h-c-box-right"></view>
					<scroll-view class="s-s-p-c" scroll-x>
						<view class="s-s--s-w" v-for="(item, index) in sizeNameLists" :key="index">
							<u-button @click="changeSize(index)" type="primary" size="mini">{{item.name}}</u-button>
						</view>
					</scroll-view>
				</view>
				<scroll-view scroll-y style="height: 100%;width: 100%;">
					<view class="page-box">
						<view v-for="(item, index) in selectedColorLists" :key="index">
							<view class="flex">
								<view>
									<u-row>
										<u-col v-if="current==0 || current==2" :span="10">
											<view style="margin-right:30rpx;">
												<u-form-item :label="item.TName" borderBottom>
													<u--input type="number" v-model="item.TQty" border="none"
														:placeholder="phColorQty"></u--input>
												</u-form-item>
											</view>
										</u-col>
										<u-col v-if="current==1" :span="10">
											{{item.TName}}
										</u-col>
										<u-col :span="2">
											<u-icon color="#ffaa00" size="24" name="trash-fill"
												@click="delColorIdx=index;showDel=true;"></u-icon>
										</u-col>
									</u-row>
								</view>
								<u--form>
								<view v-for="(size,sIdx) in item.SizeList" :key="sIdx"
									style="padding-left: 30px;padding-right: 80px;">
									<u-form-item :label="size.TName" borderBottom>
										<u--input type="number" v-model="size.TQty" border="none" :placeholder="phSizeQty"></u--input>
									</u-form-item>
								</view>
								</u--form>
							</view>
						</view>
					</view>
					<view class="form-item">
						<u--form>
						<view>
							<u-form-item label="款号名称:" labelWidth="90" borderBottom>
								<u--input v-model="selectedTicket.TName" border="none" placeholder="款号名称"></u--input>
							</u-form-item>
						</view>
						<view>
							<u-form-item label="订单编号:" labelWidth="90" borderBottom>
								<u--input v-model="selectedTicket.TSaleNo" border="none" placeholder="订单编号"></u--input>
							</u-form-item>
						</view>
						<view>
							<u-form-item label="订单制单号:" labelWidth="90" borderBottom>
								<u--input v-model="selectedTicket.TType" border="none" placeholder="订单制单号"></u--input>
							</u-form-item>
						</view>
						<view>
							<u-form-item label="备注:" labelWidth="90" borderBottom>
								<u--input v-model="selectedTicket.TRemark" border="none" placeholder="备注"></u--input>
							</u-form-item>
						</view>
						</u--form>
					</view>
					<view v-if="canEdit" class="form-item">
						<u-button type="primary" @click="showBarcodeModal">预览工菲</u-button>
					</view>
				</scroll-view>
			</view>

		</view>

	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		getParameter,
		getTicket,
		saveBarcodeLists
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				labelWidth: 100,
				ModalMsg:'',
				current: 0,
				canCreate: false,
				canEdit: false,
				showTicket: false,
				tid: 0,
				keyGuid: '',
				showPage: 0,
				itemIdx: 0,
				itemRecord: {},
				keyword: '',
				dt1: timeFormat(new Date(), 'yyyy/mm/dd'),
				showFilter: false,
				showDel: false,
				showDt1: false,
				showDt2: false,
				style: '',
				colorLists: [],
				colorNameLists: [{
					name: '重选'
				}],
				selectedColorLists: [],
				selectedSizeLists: [],
				sizeLists: [],
				sizeNameLists: [{
					name: '重选'
				}],
				ticketLists: [],
				selectedTicket: {},
				currentColor: 0,
				currentSize: 0,
				unit: '扎',
				qtyPlaceholder: '数量',
				showBarcode: false,
				showMsg: false,

				type: 0,
				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				delLists: [],
				dataLists: [],
				urlLists: [],
				queryInfo: {},
				//inputStyle:'backgroundColor:#fff;margin:4rpx;'
				inputStyle: '',
				customBtn: {
					color: 'red',
					margin: '30rpx'
				},
				tabLists: [{
					name: '方式一'
				}, {
					name: '方式二'
				}, {
					name: '方式三'
				}],
				amtQty: 0,
				amtRow: 0,
				phColorQty: '每扎数量',
				phSizeQty: '扎数',
				border: false,
				mode: 'button',
				change: true,
				labelPosition: 'left',
				delColorIdx: 0,

			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				adminWrite: 'user/adminWrite',
				adminApply: 'user/adminApply',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad() {
			this.urlLists = this.globalInfo.UrlLists
			let pages = getCurrentPages() //获取加载的页面
			let currentPage = pages[pages.length - 1] //获取当前页面的对象
			let url = "/" + currentPage.route //当前页面url
			for (let i = 0; i < this.urlLists.length; i++) {
				if (this.urlLists[i].url == url) {
					this.tabValue = i;
					break;
				}
			}
			this.canEdit=this.adminWrite[8];
			this.getParameter(0)
			this.getTicket()
		},
		onReady() {
			// 微信小程序需要用此写法
		},
		methods: {
			...mapMutations({
				setUserInfo: 'user/login',
				setGlobalInfo: 'global/set',
			}),		
			cancelFilter(){
				this.sn=''
				this.style=''
				this.showFilter=false
			},
			showBarcodeModal() {
				if (this.seleValue0 == '') {
					this.showMsg = true
					this.ModalMsg = '请先选择工序工价'
					return
				}
				this.dataLists = []
				let iRow = 1
				let amtQty = 0
				let amtRow = 0
				console.log(this.selectedColorLists)
				for (let i = 0; i < this.selectedColorLists.length; i++) {
					for (let j = 0; j < this.selectedColorLists[i].SizeList.length; j++) {
						if (this.selectedColorLists[i].SizeList[j].TQty == '' || this.selectedColorLists[i].SizeList[j]
							.TQty == null || this.selectedColorLists[i].SizeList[j].TQty == undefined)
							continue;

						//扎数循环
						if (this.current == 0) {
							for (let k = 0; k < parseInt(this.selectedColorLists[i].SizeList[j].TQty); k++) {
								this.dataLists.push({
									TNum1: iRow,
									TNum2: iRow,
									TProductGuid: this.selectedTicket.TProductGuid,
									TSaleNo: this.selectedTicket.TSaleNo,
									TType: this.selectedTicket.TType,
									TStyle: this.selectedTicket.TName,
									TRemark: this.selectedTicket.TRemark,
									TName: this.selectedTicket.TName + this.selectedColorLists[i].TName + this
										.selectedColorLists[i].SizeList[j].TName,
									TQuantity: this.selectedColorLists[i].TQty * 1.0,
									TQuantityB: this.selectedColorLists[i].TQty * 1.0,
									TColor: this.selectedColorLists[i].TName,
									TSize: this.selectedColorLists[i].SizeList[j].TName
								})
								amtQty = amtQty + parseInt(this.selectedColorLists[i].TQty)
								amtRow++
								iRow++
							}
						}
						//尺码总数量
						if (this.current == 2) {
							let QtyCount = parseInt(parseInt(this.selectedColorLists[i].SizeList[j].TQty) /
								parseInt(this.selectedColorLists[i].TQty))
							let QtyMod = parseInt(parseInt(this.selectedColorLists[i].SizeList[j].TQty) % parseInt(
								this.selectedColorLists[i].TQty))
							for (let t = 0; t < QtyCount; t++) {
								this.dataLists.push({
									TNum1: iRow,
									TNum2: iRow,
									TProductGuid: this.selectedTicket.TProductGuid,
									TSaleNo: this.selectedTicket.TSaleNo,
									TType: this.selectedTicket.TType,
									TStyle: this.selectedTicket.TName,
									TRemark: this.selectedTicket.TRemark,
									TName: this.selectedTicket.TName + this.selectedColorLists[i].TName +
										this.selectedColorLists[i].SizeList[j].TName,
									TQuantity: this.selectedColorLists[i].TQty * 1.0,
									TQuantityB: this.selectedColorLists[i].TQty * 1.0,
									TColor: this.selectedColorLists[i].TName,
									TSize: this.selectedColorLists[i].SizeList[j].TName
								})
								amtQty = amtQty + parseInt(this.selectedColorLists[i].TQty)
								amtRow++
								iRow++

							}
							if (QtyMod > 0) {
								this.dataLists.push({
									TNum1: iRow,
									TNum2: iRow,
									TProductGuid: this.selectedTicket.TProductGuid,
									TSaleNo: this.selectedTicket.TSaleNo,
									TType: this.selectedTicket.TType,
									TStyle: this.selectedTicket.TName,
									TRemark: this.selectedTicket.TRemark,
									TName: this.selectedTicket.TName + this.selectedColorLists[i].TName +
										this.selectedColorLists[i].SizeList[j].TName,
									TQuantity: QtyMod * 1.0,
									TQuantityB: QtyMod * 1.0,
									TColor: this.selectedColorLists[i].TName,
									TSize: this.selectedColorLists[i].SizeList[j].TName
								})
								amtQty = amtQty + QtyMod
								amtRow++
								iRow++
							}
						}
						//尺码数量
						if (this.current == 1) {
							this.dataLists.push({
								TNum1: iRow,
								TNum2: iRow,
								TProductGuid: this.selectedTicket.TProductGuid,
								TSaleNo: this.selectedTicket.TSaleNo,
								TType: this.selectedTicket.TType,
								TStyle: this.selectedTicket.TName,
								TRemark: this.selectedTicket.TRemark,
								TName: this.selectedTicket.TName + this.selectedColorLists[i].TName +
									this.selectedColorLists[i].SizeList[j].TName,
								TQuantity: this.selectedColorLists[i].SizeList[j].TQty * 1.0,
								TQuantityB: this.selectedColorLists[i].SizeList[j].TQty * 1.0,
								TColor: this.selectedColorLists[i].TName,
								TSize: this.selectedColorLists[i].SizeList[j].TName
							})
							amtQty = amtQty + parseInt(this.selectedColorLists[i].SizeList[j].TQty)
							amtRow++
							iRow++
						}
					}
				}
				this.amtQty = parseInt(amtQty)
				this.amtRow = parseInt(amtRow)
				this.showBarcode = true;
			},
			delColor() {
				console.log(this.delColorIdx)
				this.selectedColorLists.splice(this.delColorIdx, 1)
				this.showDel = false
			},
			cancelDelete() {
				this.showDel = false
			},
			changeCurrent(item) {
				let index = item.index
				this.current = index;
				this.swichMenu(index);
				if (index == 1) {
					this.phColorQty = '工菲数'
					this.phSizeQty = '工菲数'
					this.selectedSizeLists = []
				}
				if (index == 0 || index == 2) {
					if (index == 0) {
						this.phColorQty = '每扎数量'
						this.phSizeQty = '扎数'
					} else {
						this.phColorQty = '每扎数量'
						this.phSizeQty = '总数量'
					}
					this.setSelectedSizeList()
				}
			},
			
			changeColor(index) {
				this.currentColor = index;
				if (index == 0) {
					this.selectedColorLists = []
					this.setSelectedSizeList()
				} else {
					this.selectedColorLists.push({
						TQty: 1,
						TName: this.colorNameLists[index].name,
						SizeList: this.$u.deepClone(this.selectedSizeLists)
					})
				}
				console.log(this.selectedColorLists, this.selectedSizeLists)
			},
			changeSize(index) {
				if (this.current != 1)
					return;
				this.currentSize = index;
				if (index == 0)
					this.selectedSizeLists = []
				else
					this.selectedSizeLists.push({
						selected: false,
						TQty: 1,
						TName: this.sizeNameLists[index].name
					})
				for (let i = 0; i < this.selectedColorLists.length; i++)
					this.selectedColorLists[i].SizeList = this.$u.deepClone(this.selectedSizeLists);
			},
			setSelectedSizeList() {
				this.selectedSizeLists = []
				let sizeNameListsT = this.sizeNameLists; //JSON.stringify(this.sizeNameLists)
				for (let i = 1; i < sizeNameListsT.length; i++) {
					this.selectedSizeLists.push({
						selected: false,
						TQty: '',
						TName: sizeNameListsT[i].name
					})
				}

			},
			showDelModal() {
				this.showDel = true;
			},
			showFilterModal() {
				this.showFilter = true;
			},
			cancalFilter() {
				this.QueryFilter = this.$u.deepClone(this.QueryFilter);
			},
			
			doSearch() {
				this.dataList = []
				this.isLast = false
				this.page = 0
				this.getDataList(0)
			},
			async postSaveRecord(actType) {
				let params = {
					actType: actType,
					companyNo: this.userInfo.company_no,
					uid: this.globalInfo.DefaultUser.uid,
					itemRecord: this.itemRecord,
					dataLists: this.dataLists,
					delLists: [],
					TTicketGuid: this.selectedTicket.TGuid,
					TSaleNo: this.selectedTicket.TSaleNo||'',
					TType: this.selectedTicket.TType||'',
					TStyle: this.selectedTicket.TStyle,
					TStyleName: this.selectedTicket.TName,
					TQuantity: (this.dataLists.length - this.zeroCount) * 1.0,
					TQuantityB: this.qtyAmtT * 1.0,
					TNoStrart: this.barcodeTNoStart,
					TSn: this.barcodeTSn,
					TContent: this.guideContent,
					TCreateType: this.createType
				}
				uni.showLoading({
					'title': '正在处理操作...'
				});
				//注意提交的参数 node 无 {} 直接(params) PHP 需要({params})
				let res = await saveBarcodeLists(params);
				uni.hideLoading();

				this.showBarcode = false
				this.dataLists = []
				this.selectedColorLists = []
				uni.navigateBack()
				uni.navigateTo({
					url: '/pages/ticket/barcode'
				})
			},


			async swichMenu(current) { //点击其中一个 menu
				if (current == 0) {
					this.unit = '扎'
					this.qtyPlaceholder = '数量'
				}
				if (current == 1) {
					this.unit = ''
					this.qtyPlaceholder = '数量'

				}
				if (current == 2) {
					this.unit = '件'
					this.qtyPlaceholder = '总数'
				}

			},
			async getTicket() {
				let params = {
					companyNo: this.userInfo.company_no,
					page: 1,
					keyword:this.keyword,
					resType: 1,
					pageSize: 200
				}
				uni.showLoading({
					'title': '正在加载数据...'
				});
				this.ticketLists=[]
				this.showFilter=false
				//注意get的参数  需要({params})
				let res = await getTicket({params});
				//let data = res.dataLists
				console.log(res)
				this.ticketLists.push(res.dataLists)
				if(res.dataLists.length>0){
					this.selectedTicket=res.dataLists[0]
					//this.itemRecord.TName=this.selectedTicket.TName
				}
				uni.hideLoading();
			},
			async getParameter(type) {
				let params = {
					companyNo: this.userInfo.company_no,
					page: 1,
					resType: 1,
					pageSize: 200,
					type: type,
					ordBy: 'TSort'
				}
				uni.showLoading({
					'title': '正在加载数据...'
				});
				//注意get的参数  需要({params})
				let res = await getParameter({params});
				//let data = res.dataLists

				uni.hideLoading();
				for (let i = 0; i < res.dataLists[2].length; i++) {
					let item = res.dataLists[2][i]
					this.colorLists.push(item)
					this.colorNameLists.push({
						name: item.TName
					})
				}
				for (let i = 0; i < res.dataLists[3].length; i++) {
					let item = res.dataLists[3][i]
					this.sizeLists.push(item)
					this.sizeNameLists.push({
						name: item.TName
					})
				}
				this.setSelectedSizeList();
			},
			snSelect(e) {
				this.sn = e.name
				//this.$refs.form1.validateField('userInfo.sex')
			},
			hideKeyboard() {
				uni.hideKeyboard()
			},
			changePicker(e) {
				console.log('change', e);
				this.selectedTicket=e;
			},
			selectPicker(e) {
				console.log('confirm', e);
				this.selectedTicket = e.value[0];
				this.showTicket = false;
			},
			confirmPicker(e) {
				console.log('confirm', e);
				this.showTicket = false
			},
			cancelPicker() {
				// console.log('cancel');
				this.showTicket = false
			},
			changeHandlerPicker(e) {
				this.changePicker(e)
				const {
					columnIndex,
					value,
					values,
					index,
					// 微信小程序无法将picker实例传出来，只能通过ref操作
					picker = this.$refs.uPicker3
				} = e
				if (columnIndex === 0) {
					//多列
					//picker.setColumnValues(1, this.columnData[index])
				}
				
			},
						
		}
	}
</script>

<style lang="scss">
	.item-row {
		padding: 6rpx;
	}

	.form-item {
		padding-left: 5px;
		padding-right: 25px;
	}

	.page-box {
		margin: 5rpx;
		padding: 5rpx;
	}

	.wrap {
		display: flex;
		flex-direction: column;
		height: calc(100vh - var(--window-top));
		width: 100%;
	}

	.swiper-box {
		flex: 1;
	}

	.swiper-item {
		height: 100%;
	}

	/*scroll-view外层*/
	.s-s-p-c-wrapper {
		position: relative;
		height: 55px;
		white-space: nowrap;
		padding: 2px 10px;
	}

	/*左右渐变遮罩*/
	.h-c-box {
		position: absolute;
		top: 0;
		height: 100%;
		width: 32px;
		z-index: 2;
	}

	.h-c-box-left {
		left: 0;
		background-image: linear-gradient(to left, rgba(255, 255, 255, 0), #f3f3f3 60%);
	}

	.h-c-box-right {
		right: 0;
		background-image: linear-gradient(to right, rgba(255, 255, 255, 0), #f3f3f3 60%);
	}

	/*scroll-view本身*/
	.s-s-p-c {
		min-width: 100%;
	}

	/*scroll-view内层*/
	.s-s--s-w {
		margin-left: 2px;
		display: inline-block;
	}

	/*设置空白以保持美观*/
	.s-s-p-c .s-s--s-w:first-child {
		margin-left: 18px;
	}

	.s-s-p-c .s-s--s-w:last-child {
		margin-right: 16px;
	}
</style>

<template>
	<view class="container">
		<view class="header bg-purple-light">
			<view class="row-l-r">
				<view>建立公司</view>
				<view>
				</view>
			</view>
			
		</view>
		<view>
			<span>
				初次使用, 请您选择您的公司类型并填入公司名称完成创建公司, 其他员工绑定公司即可.
			</span>
		</view>
		<view class="body u-page__item">
			<u-action-sheet :show="false" :actions="typeLists" title="请选择公司类型" description="..."
				@close="showType = false" @select="selectPicker">
			</u-action-sheet>
			<view class="u-bg-gray from-item">
				<u--form>
					<u-form-item :labelWidth="labelWidth" label="类型" borderBottom
						@click="showType=true; hideKeyboard()">
						<u--input v-model="itemRecord.typeName" disabled disabledColor="#ffffff" placeholder="请选择公司类型"
							border="none"></u--input>
						<u-icon slot="right" name="arrow-right"></u-icon>
					</u-form-item>
				</u--form>
			</view>
				
			<view class="page-box">
				<view style="margin:5px;">
					<u--form>
						<u-form-item label="名称:" :label-width="labelWidth" borderBottom>
							<u--input v-model="itemRecord.name" border="none" placeholder="输入名称" :customStyle="inputStyle"></u--input>
						</u-form-item>
						
					</u--form>
				</view>
				<view>
					<view class="custom-btn" style="text-align: center;">
						{{msg}}
					</view>
					<view class="custom-btn">
						<u-button type="primary"  @click="save(1)">建立新公司</u-button>
					</view>
				</view>
				
			</view>
			
			<u-picker
				:show="showType"
				:columns="typeLists"
				keyName="name"
				ref="uPicker1"
				@change="changePicker"
				@cancel="cancelPicker"
				@confirm="selectPicker"
			></u-picker>
			
		</view>

	</view>
</template>

<script>
	import {
		mapGetters,
		mapMutations
	} from 'vuex';
	import {
		astCf,
		dbInitParameter
	} from '@/common/api.js';
	export default {
		data() {
			const timeFormat = uni.$u.timeFormat
			const d = new Date()
			const year = d.getFullYear()
			let month = uni.$u.padZero(d.getMonth() + 1)
			const date = d.getDate()
			return {
				typeLists:[[{name:'服装行业',value:0},{name:'电子五金',value:1},{name:'箱包皮具',value:2},{name:'帽子手套',value:3},{name:'家具门窗',value:4}]],
				labelWidth: 50,
				msg:'',
				quantity: 1,
				current: 0,
				showPage: 0,
				itemIdx: 0,
				itemRecord: {type:0,name:'',typeName:'服装行业'},
				keyword: '',
				//minDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -730))),
				//maxDate: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), 365))),
				dt1: Number(new Date(this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30))),
				dt2: Number(new Date()),
				textDt1: this.addDate(timeFormat(new Date(), 'yyyy/mm/dd'), -30),
				textDt2: timeFormat(new Date(), 'yyyy/mm/dd'),
				showType: false,
				showFilter: false,
				showDel: false,
				showDt1: false,
				showDt2: false,
				showTSn: false,
				showStyle: false,
				sn: '',
				style: '',
				snLists: [{
						name: '000001',
					}
				],
				styleLists: [{
						name: '01',
					}
				],
				selectedType:{},
				tabValue: 0,
				loadStatus: 'loadmore',
				page: 1,
				dataLists: [],
				queryInfo: {},
				inputStyle:'',
				customBtn:{
					color:'red',
					margin:'30rpx'
				}

			}
		},
		computed: {
			...mapGetters({
				userInfo: 'user/info',
				hasLogin: 'user/hasLogin',
				globalInfo: 'global/get'
			})
		},
		onLoad() {
			//this.loadmore();
		},
		onReady() {
			// 微信小程序需要用此写法
			////this.$refs.dtP1.setFormatter(this.formatter)
			////this.$refs.dtP2.setFormatter(this.formatter)
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
			async save(actType){
				this.msg='';
				
				let params = {
					actType: actType,
					action:'appRegisterCompany',
					uid:this.userInfo.uid,
					type:this.itemRecord.type,
					corpname:this.itemRecord.name
				}
				uni.showLoading({
					'title': '正在处理操作...'
				});
				//post wu {}
				let info = await astCf(params);
				uni.hideLoading();
				
				this.msg=info.msg;
				if (info.code === 0) {
					this.setUserInfo(info.userInfo);
					await dbInitParameter({
						params: {}
					});
					uni.navigateBack();
				}
				
			},
			async configDelete(){
				this.save(2)
			},
			cancelDelete(){
				this.showDel=false
			},
			navigateTo(url) {
				//uni.redirectTo({
				uni.navigateTo({
					url
				})
			},
			switchTab(url) {
				uni.switchTab({
					url
				})
			},
			changeTabbar(e) {
				this.tabValue = e
				var url = this.urlLists[e].url
				uni.redirectTo({
					url
				})
			},
			close() {
				if (this.current == 1)
					this.showDt1 = false
				if (this.current == 2)
					this.showDt2 = false
			},
			cancel(idx) {
				this.close()
			},
			confirm(e) {
				this.close()
				const timeFormat = uni.$u.timeFormat
				if (this.current == 1) {
					//this.dt1 = timeFormat(e.value, 'yyyy/mm/dd')
					this.textDt1 = timeFormat(e.value, 'yyyy/mm/dd')
				}
				if (this.current == 2) {
					//this.dt2 = timeFormat(e.value, 'yyyy/mm/dd')
					this.textDt2 = timeFormat(e.value, 'yyyy/mm/dd')
				}

			},
			change(e) {
				//
			},
			formatter(type, value) {
				if (type === 'year') {
					return `${value}年`
				}
				if (type === 'month') {
					return `${value}月`
				}
				if (type === 'day') {
					return `${value}日`
				}
				return value
			},
			snSelect(e) {
				this.sn = e.name
				//this.$refs.form1.validateField('userInfo.sex')
			},
			styleSelect(e) {
				this.style = e.name
			},
			hideKeyboard() {
				uni.hideKeyboard()
			},
			changePicker(e) {
				console.log('change', e);
			},			
			selectPicker(e) {
				this.selectedType = e.value[0];
				this.itemRecord.type=this.selectedType.value
				this.itemRecord.typeName=this.selectedType.name
				this.showType = false;
				console.log(this.selectedType)
			},
			confirmPicker(e) {
				console.log('confirm', e);
				this.showType = false
			},
			cancelPicker() {
				// console.log('cancel');
				this.showType = false
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
			}
		}
	}
</script>

<style lang="scss">
	.item-row {
		padding: 6rpx;
	}
</style>

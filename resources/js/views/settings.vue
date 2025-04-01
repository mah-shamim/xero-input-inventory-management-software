<template>
  <v-container fluid>
    <v-card flat>
      <form @submit.prevent="postSettings('settings')" data-vv-scope="settings">
        <v-card-title>Application Detail & Settings</v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="4" cols="12">
              <v-text-field
                  hint="site name"
                  persistent-hint
                  name="site_name"
                  dusk="site_name"
                  label="site name"
                  v-validate="{required:true}"
                  v-model="$store.state.settings.settings.site_name"
                  :error-messages="errors.collect('settings.site_name')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  hint="currency"
                  persistent-hint
                  name="currency"
                  dusk="currency"
                  label="currency"
                  data-vv-name="currency"
                  v-validate="{required:true}"
                  v-model="$store.state.settings.settings.currency"
                  :error-messages="errors.collect('settings.currency')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  persistent-hint
                  name="default_email"
                  dusk="default_email"
                  hint="default email"
                  label="default email"
                  v-validate="{required:true}"
                  data-vv-name="default_email"
                  data-vv-as="Default Email"
                  v-model="$store.state.settings.settings.default_email"
                  :error-messages="errors.collect('settings.default_email')"
              />
            </v-col>
          </v-row>
          <v-row>
            <v-col md="6" cols="12">
              <v-text-field
                  readonly
                  @click="menu=true"
                  name="sidebar_color"
                  dusk="sidebar_color"
                  label="sidebar color"
                  data-vv-as="Sidebar Color"
                  v-validate="{required:true}"
                  data-vv-name="settings.design.sidebar_color"
                  v-model="$store.state.settings.settings.design.sidebar_color"
                  :error-messages="errors.collect('settings.design.sidebar_color')"
              >
                <template v-slot:append>
                  <v-menu
                      top
                      v-model="menu"
                      nudge-left="16"
                      nudge-bottom="105"
                      :close-on-content-click="false"
                  >
                    <template v-slot:activator="{ on }">
                      <div :style="swatchStyle" v-on="on"/>
                    </template>
                    <v-card>
                      <v-card-text class="pa-0">
                        <v-color-picker
                            show-swatches
                            v-model="$store.state.settings.settings.design.sidebar_color"
                        />
                      </v-card-text>
                    </v-card>
                  </v-menu>
                </template>
              </v-text-field>
            </v-col>
            <v-col md="6" cols="12">
              <v-text-field
                  readonly
                  @click="menu1=true"
                  name="topbar_color"
                  dusk="topbar_color"
                  label="topbar color"
                  data-vv-as="Topbar Color"
                  v-validate="{required:true}"
                  data-vv-name="settings.design.topbar_color"
                  v-model="$store.state.settings.settings.design.topbar_color"
                  :error-messages="errors.collect('settings.design.topbar_color')"
              >
                <template v-slot:append>
                  <v-menu
                      top
                      v-model="menu1"
                      nudge-left="16"
                      id="topbar_color"
                      nudge-bottom="105"
                      :close-on-content-click="false"
                  >
                    <template v-slot:activator="{ on }">
                      <div :style="swatchStyle1" v-on="on"/>
                    </template>
                    <v-card>
                      <v-card-text class="pa-0">
                        <v-color-picker
                            show-swatches
                            v-model="$store.state.settings.settings.design.topbar_color"
                        />
                      </v-card-text>
                    </v-card>
                  </v-menu>
                </template>
              </v-text-field>
            </v-col>
          </v-row>
          <v-row>
            <v-col md="3" cols="12" id="account_method">
              <v-select
                  persistent-hint
                  name="account_method"
                  dusk="account_method"
                  hint="accounting method"
                  label="accounting method"
                  data-vv-as="Account Method"
                  v-validate="{required:true}"
                  :items="$store.state.accountingMethod"
                  data-vv-name="settings.inventory.profit_percent"
                  v-model="$store.state.settings.settings.inventory.account_method"
                  :error-messages="errors.collect('settings.inventory.account_method')"
              />
            </v-col>
            <v-col md="3" cols="12" id="profit_percent">
              <v-select
                  persistent-hint
                  name="profit_percent"
                  dusk="profit_percent"
                  hint="profit % on sale"
                  data-vv-as="Profit Percent"
                  v-validate="{required:true}"
                  data-vv-name="settings.inventory.profit_percent"
                  v-model="$store.state.settings.settings.inventory.profit_percent"
                  :items="[{value:false, text:'false'}, {value:true, text:'true'}]"
                  :error-messages="errors.collect('settings.inventory.profit_percent')"
              />
            </v-col>
            <v-col md="3" cols="12">
              <v-text-field
                  maxlength="25"
                  persistent-hint
                  name="shipping_cost_label"
                  dusk="shipping_cost_label"
                  v-validate="{required:true}"
                  data-vv-as="Shipping Cost Label"
                  label="shipping cost label change"
                  hint="you can change shipping cost label"
                  data-vv-name="settings.inventory.shipping_cost_label"
                  v-model="$store.state.settings.settings.inventory.shipping_cost_label"
                  :error-messages="errors.collect('settings.inventory.shipping_cost_label')"
              />
            </v-col>
            <v-col md="3" cols="12">
              <v-text-field
                  maxlength="25"
                  persistent-hint
                  name="quantity_label"
                  dusk="quantity_label"
                  data-vv-as="Quantity Label"
                  v-validate="{required:true}"
                  label="quantity label change"
                  hint="you can change quantity label"
                  data-vv-name="settings.inventory.quantity_label"
                  v-model="$store.state.settings.settings.inventory.quantity_label"
                  :error-messages="errors.collect('settings.inventory.quantity_label')"
              />
            </v-col>
          </v-row>
          <v-divider class="my-6"/>
          <v-row>
            <v-col md="6" cols="12" class="tw-bg-gray-200 tw-rounded-md">
              <legend class="text-h6"><strong>Purchase:</strong></legend>
              <v-row>
                <v-col md="6" cols="12" id="purchase_payment_method">
                  <v-select
                      item-text="name"
                      item-value="id"
                      v-validate="{required:true}"
                      name="purchase_payment_method"
                      dusk="purchase_payment_method"
                      label="Default Payment Method"
                      data-vv-as="Default Payment Method"
                      :items="$store.state.paymentMethods"
                      data-vv-name="settings.inventory.purchase.default_payment_mood"
                      v-model="$store.state.settings.settings.inventory.purchase.default_payment_mood"
                  />
                </v-col>
              </v-row>
            </v-col>
            <v-col md="6" cols="12" class="tw-bg-gray-100 tw-rounded-md">
              <legend class="text-h6"><strong>Sale:</strong></legend>
              <v-row>
                <v-col md="6" cols="12" id="sale_payment_method">
                  <v-select
                      item-text="name"
                      item-value="id"
                      name="sale_payment_method"
                      dusk="sale_payment_method"
                      v-validate="{required:true}"
                      label="Default Payment Method"
                      data-vv-as="Default Payment Method"
                      :items="$store.state.paymentMethods"
                      data-vv-name="settings.inventory.sale.default_payment_mood"
                      v-model="$store.state.settings.settings.inventory.sale.default_payment_mood"
                  />
                </v-col>
                <v-col md="6" cols="12" id="stock_out_sale">
                  <v-select
                      persistent-hint
                      name="stock_out_sale"
                      dusk="stock_out_sale"
                      hint="stock out sale"
                      label="stock our sale"
                      data-vv-as="Stock Out Sale"
                      v-validate="{required:true}"
                      data-vv-name="settings.inventory.stock_out_sale"
                      :items="[{value:true, text:'true'}, {value:false, text:'false'}]"
                      v-model="$store.state.settings.settings.inventory.sale.stock_out_sale"
                      :error-messages="errors.collect('settings.inventory.stock_out_sale')"
                  />
                </v-col>
              </v-row>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn
              outlined
              type="submit"
              color="success"
              v-text="'Submit'"
              dusk="submit_settings"
          />
        </v-card-actions>
      </form>
    </v-card>
    <br>
    <v-divider></v-divider>
    <br>
    <v-card outlined id="company_detail">
      <form @submit.prevent="postCompanyDetail('company_detail')" data-vv-scope="company_detail">
        <v-card-title>Change Company Detail</v-card-title>
        <v-card-text>
          <v-row>
            <v-col md="4" cols="12">
              <v-text-field
                  type="text"
                  name="name"
                  dusk="cd_name"
                  persistent-hint
                  hint="Company Name"
                  label="Company Name"
                  data-vv-as="Company Name"
                  v-validate="{required:true}"
                  data-vv-name="company_detail.name"
                  v-model="$store.state.settings.company_detail.name"
                  :error-messages="errors.collect('company_detail.name')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  type="text"
                  name="address1"
                  persistent-hint
                  dusk="cd_address1"
                  hint="Primary Address"
                  label="Primary Address"
                  data-vv-as="Primary Address"
                  v-validate="{required:true}"
                  data-vv-name="company_detail.address1"
                  v-model="$store.state.settings.company_detail.address1"
                  :error-messages="errors.collect('company_detail.address1')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  type="text"
                  name="address2"
                  persistent-hint
                  dusk="cd_address2"
                  hint="Secondary Address"
                  label="Secondary Address"
                  v-validate="{required:true}"
                  data-vv-as="Secondary Address"
                  data-vv-name="company_detail.address2"
                  v-model="$store.state.settings.company_detail.address2"
                  :error-messages="errors.collect('company_detail.address2')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  type="text"
                  name="code"
                  hint="Code"
                  label="Code"
                  dusk="cd_code"
                  persistent-hint
                  data-vv-as="Code"
                  v-validate="{required:true}"
                  data-vv-name="company_detail.code"
                  v-model="$store.state.settings.company_detail.code"
                  :error-messages="errors.collect('company_detail.code')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  type="url"
                  name="web_url"
                  dusk="cd_web_url"
                  persistent-hint
                  hint="Website Url"
                  label="Website Url"
                  data-vv-as="Website Url"
                  data-vv-name="company_detail.web_url"
                  v-validate="{required:false, url:true}"
                  v-model="$store.state.settings.company_detail.web_url"
                  :error-messages="errors.collect('company_detail.web_url')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <v-text-field
                  type="text"
                  persistent-hint
                  name="contact_name"
                  hint="Contact Name"
                  label="Contact Name"
                  dusk="cd_contact_name"
                  data-vv-as="Contact Name"
                  v-validate="{required:true}"
                  data-vv-name="company_detail.contact_name"
                  v-model="$store.state.settings.company_detail.contact_name"
                  :error-messages="errors.collect('company_detail.contact_name')"
              />
            </v-col>
            <v-col md="4" cols="12">
              <div v-for="(num, index) in $store.state.settings.company_detail.contact_phone">
                <v-text-field
                    persistent-hint
                    hint="contact phone"
                    dusk="contact_phone"
                    label="Contact numbers"
                    :append-icon="$root.icons.delete"
                    @click:append="$store.commit('settings/deleteNumber', index)"
                    v-model="$store.state.settings.company_detail.contact_phone[index]"
                />
              </div>
              <v-btn
                  dusk="addNumber"
                  @click="$store.commit('settings/addNumber')"
              >
                Add more
              </v-btn>
            </v-col>
          </v-row>
        </v-card-text>
        <v-card-actions>
          <v-btn
              type="submit"
              v-text="'Submit'"
              outlined color="success"
              dusk="cd_company_detail_submit"
          />
        </v-card-actions>
      </form>
    </v-card>
  </v-container>
</template>
<script src="./settings.js"/>
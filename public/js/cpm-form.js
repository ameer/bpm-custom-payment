"use strict";
var cpmApp = new Vue({
  el: "#cpm-payment-form",
  data: {
    loading: false,
    showConfirm: false,
    showErrors: false,
    customAmount: false,
    orderId: '',
    selected: -1,
    amount: "",
    mobileNumber: "",
    fullname: "",
    nationalCode: "",
    RefId: '',
    paymentURL: ''
  },
  validations: {
    fullname: { required },
    mobileNumber: {
      required,
      phone,
      numeric,
      minLength: minLength(11),
      maxLength: maxLength(11),
    },
    nationalCode: {
      required,
      numeric,
      minLength: minLength(10),
      maxLength: maxLength(10),
      nationalCodeChecker
    },
    amount: {
      required,
      numeric
    },
  },
  computed: {
    amountInFa() {
      return (this.amount).num2persian() + " ریال"
    }
  },
  methods: {
    status(validation) {
      return {
        error: validation.$error,
        dirty: validation.$dirty,
      };
    },
    validate() {
      const self = this;
      if (self.$v.$invalid) {
        self.showErrors = true;
        return false;
      } else {
        self.showErrors = false;
        return true
      }
    },
    submitPayment() {
      const self = this;
      if (!self.validate()) {
        return;
      }
      const params = {
        action: "cpm_ajax_handler",
        func: "submitPayment",
        amount: self.amount,
        fullname: self.fullname,
        mobileNumber: self.mobileNumber,
        nationalCode: self.nationalCode,
        _nonce: cpm._nonce,
      };
      self.loading = true;
      jQuery.ajax({
        cache: false,
        type: "POST",
        url: cpm.ajax_url,
        data: params,
        success: function (response) {
          const json_resp = JSON.parse(response);
          if (json_resp.ok == true) {
            self.showConfirm = true;
            self.RefId = json_resp.RefId;
            self.paymentURL= json_resp.url;
            self.orderId = json_resp.orderId;
          } else {
            alert(json_resp.msg);
          }
          self.loading = false;
        },
        error: function (xhr, status, error) {
          console.log("Status: " + xhr.status);
          console.log("Error: " + xhr.responseText);
          self.loading = false;
        },
      });
    },
  },
});


<template>
  <div>
    <h1>{{$t('dashboard.title')}}</h1>
      <!-- error message -->
      <div v-if="errorMessage" class="alert alert-danger alert-dismissable">
        <button type="button" class="close" @click="closeErrorMessage()" aria-label="Close">
          <span class="pficon pficon-close"></span>
        </button>
        <span class="pficon pficon-error-circle-o"></span>
        {{ errorMessage }}.
      </div>

      <div v-show="!uiLoaded" class="spinner spinner-lg"></div>
      <div v-show="uiLoaded">
        <div id="pie-chart-users" v-show="dashboardData.hotspotUsers.length > 0"></div>
        <div v-show="dashboardData.hotspotUsers.length == 0" class="empty-piechart">
          <div class="fa fa-pie-chart"></div>
          <div>{{$t('dashboard.no_user_connected')}}</div>
        </div>

        <vue-good-table 
          :customRowsPerPageDropdown="[25,50,100]"
          :perPage="25"
          :columns="tableColumns"
          :rows="dashboardData.hotspotUsers"
          :lineNumbers="false"
          :defaultSortBy="{field: 'ipAddress', type: 'asc'}"
          :globalSearch="true"
          :paginate="true"
          styleClass="table"
          :nextText="tableLangsTexts.nextText"
          :prevText="tableLangsTexts.prevText"
          :rowsPerPageText="tableLangsTexts.rowsPerPageText"
          :globalSearchPlaceholder="tableLangsTexts.globalSearchPlaceholder"
          :ofText="tableLangsTexts.ofText"
        >
          <template slot="table-row" slot-scope="props">
            <td class="fancy">
              <a href="#" 
                v-if="props.row.status === 'pass'"
                data-toggle="popover"
                data-html="true"
                :title="$t('dashboard.user_info')"
                :id="'popover-' + props.row.ipAddress | sanitize"
                @click="getIpAddressInfo(props.row.ipAddress)"
              >
                {{ props.row.macAddress }}
              </a>
              <span v-else>{{ props.row.macAddress }}</span>
            </td>
            <td class="fancy">
              {{ props.row.ipAddress}}
            </td>
            <td class="fancy">
              <span :class="['fa', props.row.status === 'pass' ? 'fa-check green' : 'fa-times red']"></span>
            </td>
            <td class="fancy">
              {{ props.row.sessionKey }}
            </td>
            <td class="fancy">
              {{ readableDuration(parseInt(props.row.sessionTimeElapsed)) }}
            </td>
            <td class="fancy">
              {{ readableDuration(parseInt(props.row.idleTimeElapsed)) }}
            </td>
            <td class="fancy">
              {{ readableBytes(parseInt(props.row.inputOctetsDownloaded)) }}
            </td>
            <td class="fancy">
              {{ readableBytes(parseInt(props.row.outputOctetsUploaded)) }}
            </td>
          </template>
        </vue-good-table>
      </div>
  </div>
</template>

<script>
export default {
  name: "Dashboard",
  props: {
  },
  mounted() {
    this.getToken()
  },
  data() {
    return {
      uiLoaded: false,
      errorMessage: null,
      tableLangsTexts: this.tableLangs(),
      dashboardData: {
        hotspotUsers: []
      },
      token: '',
      icaroHost: '',
      authenticated: false,
      popoverShown: [],
      tableColumns: [
        {
          label: this.$i18n.t("dashboard.mac_address"),
          field: "macAddress",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.ip_address"),
          field: "ipAddress",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.connected"),
          field: "status",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.session_key"),
          field: "sessionKey",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.session_time"),
          field: "sessionTimeElapsed",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.idle_time"),
          field: "idleTimeElapsed",
          filterable: true
        },
        {
          label: this.$i18n.t("dashboard.downloaded"),
          field: "inputOctetsDownloaded"
        },
        {
          label: this.$i18n.t("dashboard.uploaded"),
          field: "outputOctetsUploaded"
        }
      ]
    };
  },
  methods: {
    readDashboardData() {
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/dashboard/read"],
        { "appInfo": "dashboardData" },
        null,
        function(success) {
          var dashboardOutput = JSON.parse(success);
          ctx.readDashboardDataSuccess(dashboardOutput)
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("dashboard.error_retrieving_dashboard_data"), error)
        }
      );
    },
    readDashboardDataSuccess(dashboardOutput) {
      this.dashboardData = dashboardOutput.dashboardData
      this.initUsersChart()
      this.uiLoaded = true
      this.initPopovers()
    },
    showErrorMessage(errorMessage, error) {
      console.error(errorMessage, error) /* eslint-disable-line no-console */
      this.errorMessage = errorMessage
    },
    closeErrorMessage() {
      this.errorMessage = null
    },
    readableBytes(bytes) {
      if (bytes == 0) {
        return '0 B'
      }
      var i = Math.floor(Math.log(bytes) / Math.log(1024)),
      sizes = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
      return (bytes / Math.pow(1024, i)).toFixed(2) * 1 + ' ' + sizes[i];
    },
    readableDuration(sec_num) {
      var hours   = Math.floor(sec_num / 3600);
      var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
      var seconds = sec_num - (hours * 3600) - (minutes * 60);

      if (hours   < 10) {hours   = "0"+hours;}
      if (minutes < 10) {minutes = "0"+minutes;}
      if (seconds < 10) {seconds = "0"+seconds;}
      return hours+':'+minutes+':'+seconds;
    },
    initUsersChart() {
      var c3ChartDefaults = $().c3ChartDefaults();

      var pieData = {
        type : 'pie',
        colors: {
          'dnat users': $.pfPaletteColors.blue,
          'pass users': $.pfPaletteColors.green
        },
        columns: [
          ['dnat users', parseInt(this.dashboardData.dnatUsers)],
          ['pass users', parseInt(this.dashboardData.passUsers)]
        ]
      };

      var pieChartConfig = c3ChartDefaults.getDefaultPieConfig();
      pieChartConfig.bindto = '#pie-chart-users';
      pieChartConfig.data = pieData;
      pieChartConfig.legend = {
        show: true,
        position: 'right'
      };
      pieChartConfig.size = {
        width: 301,
        height: 211
      };
      var pieChartLegend = c3.generate(pieChartConfig);
    },
    getIpAddressInfo(ipAddress) {
      var popoverId = "#" + this.$options.filters.sanitize("popover-" + ipAddress)
      var popover = $(popoverId).data("bs.popover");

      if (!this.authenticated) {
        popover.options.content = this.$i18n.t("dashboard.please_authenticate_to_retrieve_user_info")
      } else {
        if (this.popoverShown[popoverId] != true) {
          // show spinner on popover
          popover.options.content = '<div class="spinner spinner-sm"></div>';
          popover.show();

          var ctx = this;
          nethserver.exec(
            ["nethserver-dedalo/dashboard/read"],
            {
              "appInfo": "ipAddressInfo",
              "ipAddress": ipAddress,
              "token": this.token,
              "icaroHost": this.icaroHost
            },
            null,
            function(success) {
              var ipAddressInfoOutput = JSON.parse(success);
              ctx.getIpAddressInfoSuccess(ipAddressInfoOutput, popoverId)
            },
            function(error) {
              popover.options.content = ctx.$i18n.t("dashboard.error_retrieving_user_info")
              console.error(error) /* eslint-disable-line no-console */
            }
          );
        }
      }
    },
    getIpAddressInfoSuccess(ipAddressInfoOutput, popoverId) {
      var ipAddressInfo = ipAddressInfoOutput.ipAddressInfo
      var popover = $(popoverId).data("bs.popover");
      
      if (ipAddressInfo.message) {
        // an error occured
        popover.options.content = this.$i18n.t("dashboard.error_retrieving_user_info")
        this.popoverShown[popoverId] = true;
        popover.show();
        console.error(ipAddressInfo.message)
      } else {
        var userId = ipAddressInfo.data[0].user_id
        var ctx = this;
        nethserver.exec(
          ["nethserver-dedalo/dashboard/read"],
          {
            "appInfo": "userInfo",
            "userId": userId,
            "token": this.token,
            "icaroHost": this.icaroHost
          },
          null,
          function(success) {
            var userInfoOutput = JSON.parse(success);
            ctx.getUserInfoSuccess(userInfoOutput, popoverId)
          },
          function(error) {
            popover.options.content = ctx.$i18n.t("dashboard.error_retrieving_user_info")
            console.error(error) /* eslint-disable-line no-console */
          }
        );
      }
    },
    getUserInfoSuccess(userInfoOutput, popoverId) {
      var userInfo = userInfoOutput.userInfo
      var popover = $(popoverId).data("bs.popover");

      if (userInfo.message) {
        // an error occured
        popover.options.content = this.$i18n.t("dashboard.error_retrieving_user_info")
        this.popoverShown[popoverId] = true;
        popover.show();
        console.error(userInfo.message)
      } else {
        popover.options.content = '<p>' + this.$i18n.t("dashboard.user_info_name") + ': <b>' + userInfo.name + '</b></p>' + 
                        '<p>' + this.$i18n.t("dashboard.user_info_email") + ': <b>' + userInfo.email + '</b></p>' +
                        '<p>' + this.$i18n.t("dashboard.user_info_account_type") + ': <b>' + userInfo.account_type + '</b></p>'

        this.popoverShown[popoverId] = true;
        popover.show();
      }
    },
    getToken() {
      var ctx = this;
      nethserver.exec(
        ["nethserver-dedalo/authentication/read"],
        { "appInfo": "token" },
        null,
        function(success) {
          var tokenOutput = JSON.parse(success);
          ctx.tokenSuccess(tokenOutput)
          
        },
        function(error) {
          ctx.showErrorMessage(ctx.$i18n.t("settings.error_retrieving_token_from_file"), error)
        }
      );
    },
    tokenSuccess(tokenOutput) {
      var tokenData = tokenOutput.tokenData
      this.token = tokenData.token;
      this.icaroHost = tokenData.icaroHost;

      if (this.token) {
        this.authenticated = true
      } else {
        this.authenticated = false
      }
      this.readDashboardData()
    },
    initPopovers() {
      // Initialize Popovers
      setTimeout(function() {
        $('[data-toggle=popover]').popovers()
          .on('hidden.bs.popover', function (e) {
            $(e.target).data('bs.popover').inState.click = false;
        });
      }, 250)
    }
  }
};
</script>

<style>
.red {
    color: #cc0000;
}

.green {
    color: #3f9c35;
}

#pie-chart-users {
  width: 301px;
  margin-left: auto;
  margin-right: auto;
  margin-bottom: 20px;
}

.empty-piechart {
  margin: 2em;
  text-align: center;
  color: #9c9c9c;
}

.empty-piechart .fa {
  font-size: 200px;
  color: #bbbbbb;
}
</style>

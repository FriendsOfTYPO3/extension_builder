// console.log("Hello from extensionbuilder.js");
define([
  'jquery',
  'TYPO3/CMS/ExtensionBuilder/Contrib/vue',
  'TYPO3/CMS/Backend/Notification',
  'TYPO3/CMS/Backend/Modal'
], function ($, Vue, Notification, Modal) {
  if (!document.getElementById('modeller')) {
    return;
  }

  new Vue({
    el: '#modeller',
    data: function() {
      return {
        mode: 'overview',
        extension: [
          {
            title: "Create extension",
            bodytext: "With this tool you can create a whole extension including viewhelper, widgets and scheduler tasks.",
            linktext: "Create extension",
            linkhref: "ExtensionPage",
            linkenabled: true,
            buttonstyle: "btn btn-success",
            icon: "content-extension",
            footertext: ""
          },
          {
            title: "Create schuduler task",
            bodytext: "Sed lectus. Vestibulum suscipit nulla quis orci. Praesent congue erat at massa. Vestibulum suscipit nulla quis orci. Suspendisse feugiat.",
            linktext: "Create scheduler task",
            linkhref: "SchedulerTaskPage",
            linkenabled: false,
            buttonstyle: "btn btn-danger",
            icon: "mimetypes-x-tx_scheduler_task_group",
            footertext: "Coming soon"
          },
          {
            title: "Create viewhelper",
            bodytext: "Sed lectus. Vestibulum suscipit nulla quis orci. Praesent congue erat at massa. Vestibulum suscipit nulla quis orci. Suspendisse feugiat.",
            linktext: "Create viewhelper",
            linkhref: "ViewhelperPage",
            linkenabled: false,
            buttonstyle: "btn btn-danger",
            icon: "module-viewpage",
            footertext: "Coming soon"
          },
          {
            title: "Create widgets",
            bodytext: "Sed lectus. Vestibulum suscipit nulla quis orci. Praesent congue erat at massa. Vestibulum suscipit nulla quis orci. Suspendisse feugiat.",
            linktext: "Create widgets",
            linkhref: "WidgetsPage",
            linkenabled: false,
            buttonstyle: "btn btn-danger",
            icon: "content-widget-chart-pie",
            footertext: "Coming soon"
          }
        ],
      }
    },
    methods: {
      changeActiveView(componentName) {
        console.log("This should change the active view");
        console.log(componentName);
      }
    },
    // mutations: {
    //   setActiveView(view) {
    //     state.activeView = view;
    //   }
    // },
    computed: {
      activeView() {
        console.log("Active View()");
      }
    }
  });
  console.log(Vue);
});

import DashboardLayout from '../components/Dashboard/Layout/DashboardLayout.vue'
// GeneralViews
import NotFound from '../components/GeneralViews/NotFoundPage.vue'

// Admin pages
import Overview from '../components/Dashboard/Views/Overview.vue'
import UserProfile from '../components/Dashboard/Views/UserProfile.vue'
import Notifications from '../components/Dashboard/Views/Notifications.vue'
import Icons from '../components/Dashboard/Views/Icons.vue'
import Maps from '../components/Dashboard/Views/Maps.vue'
import Typography from '../components/Dashboard/Views/Typography.vue'
import TableList from '../components/Dashboard/Views/TableList.vue'

// CSI
import RelatoriosAtendimento from '../components/Dashboard/Views/RelatoriosAtendimento.vue'
import RelatoriosCDR from '../components/Dashboard/Views/RelatoriosCDR.vue'

const routes = [
  // {
  //   path: '/',
  //   component: DashboardLayout,
  //   redirect: '/overview'
  // },
  {
    path: '/',
    component: DashboardLayout,
    redirect: '/relatorios/atendimento',
    children: [
      {
        path: '/relatorios/cdr',
        name: 'Relatórios de Chamadas',
        component: RelatoriosCDR
      },
      {
        path: '/relatorios/atendimento',
        name: 'Relatórios de Atendimento',
        component: RelatoriosAtendimento
      },
      {
        path: 'stats',
        name: 'stats',
        component: UserProfile
      },
      {
        path: 'notifications',
        name: 'notifications',
        component: Notifications
      },
      {
        path: 'icons',
        name: 'icons',
        component: Icons
      },
      {
        path: 'maps',
        name: 'maps',
        component: Maps
      },
      {
        path: 'typography',
        name: 'typography',
        component: Typography
      },
      {
        path: 'table-list',
        name: 'table-list',
        component: TableList
      }
    ]
  },
  { path: '*', component: NotFound }
]

/**
 * Asynchronously load view (Webpack Lazy loading compatible)
 * The specified component must be inside the Views folder
 * @param  {string} name  the filename (basename) of the view to load.
function view(name) {
   var res= require('../components/Dashboard/Views/' + name + '.vue');
   return res;
};**/

export default routes

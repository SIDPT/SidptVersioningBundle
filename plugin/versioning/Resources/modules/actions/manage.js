import {trans} from '#/main/app/intl/translation'
import {MODAL_BUTTON} from '#/main/app/buttons'

import {MODAL_VERSIONS_MANAGE} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/'


export default (resourceNodes, nodesRefresher, path) => ({
  name: 'manage_versions',
  type: MODAL_BUTTON,
  icon: 'fa fa-fw fa-info',
  label: trans('versions.manage', {}, 'versioning'),
  modal: [MODAL_VERSIONS_MANAGE, {
    node: resourceNodes[0],
    path:path
  }]
})

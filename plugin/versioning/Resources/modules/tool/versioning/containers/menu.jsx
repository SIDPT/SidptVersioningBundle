import {connect} from 'react-redux'

import {withRouter} from '#/main/app/router'
import {selectors as formSelect} from '#/main/app/content/form/store/selectors'
import {selectors as resourcesSelectors} from '#/main/core/resource/store'

import {hasPermission} from '#/main/app/security'

import {VersioningMenu as VersioningMenuComponent} from '~/sidpt/versioning-bundle/plugin/versioning/tool/dashboard/components/menu'
import {selectors} from '~/sidpt/versioning-bundle/plugin/versioning/tool/versioning/store'

const VersioningdMenu = withRouter(
  connect(
    (state) => ({
      
    })
  )(VersioningdMenuComponent)
)

export {
  VersioningdMenu
}


import {VersioningTool} from '~/sidpt/versioning-bundle/plugin/versioning/tool/versioning/containers/tool'
import {VersioningMenu} from '~/sidpt/versioning-bundle/plugin/versioning/tool/versioning/containers/menu'
import {reducer} from '~/sidpt/versioning-bundle/plugin/versioning/tool/versioning/store'

/**
 * VersioningTool application.
 */
export default {
  component: VersioningTool,
  menu: VersioningMenu,
  store: reducer,
  styles: ['sidpt-versioning-plugin-versioning-versioning-tool']
}

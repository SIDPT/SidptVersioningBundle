/**
 * Resource About modal.
 * Displays general information about the resource.
 */

import {registry} from '#/main/app/modals/registry'

// gets the modal container
import {VersionsManagingModal} from '~/sidpt/versioning-bundle/plugin/versioning/modals/manage/containers/manage'

const MODAL_VERSIONS_MANAGE = 'MODAL_VERSIONS_MANAGE'

// make the modal available for use
registry.add(MODAL_VERSIONS_MANAGE, VersionsManagingModal)

export {
  MODAL_VERSIONS_MANAGE,
  VersionsManagingModal
}

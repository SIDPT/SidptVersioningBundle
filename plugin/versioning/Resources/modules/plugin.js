/* eslint-disable */

import {registry} from '#/main/app/plugins/registry'

registry.add('SidptVersioningBundle', {
  actions: {
    resource: {
      'manage_versions': () => { return import(/* webpackChunkName: "versioning-action-manage-versions" */ '~/sidpt/versioning-bundle/plugin/versioning/actions/manage') }
    }
  }
})

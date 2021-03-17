/* eslint-disable */

import {registry} from '#/main/app/plugins/registry'

/*
tools: {
    'sidpt_versioning': () => { return import( webpackChunkName: "plugin-sidpt-versioning-tool"  '~/sidpt/versioning-bundle/plugin/versioning/tool/versioning') }  },
 */

registry.add('SidptVersioningBundle', {
  actions: {
    resource: {
      'manage_versions': () => { return import(/* webpackChunkName: "plugin-sidpt-versioning-action-manage-versions" */ '~/sidpt/versioning-bundle/plugin/versioning/actions/manage_versions') }
    }
  }
})

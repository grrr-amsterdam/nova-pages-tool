import SeoMedia from './fields/SeoMeta/SeoMedia';
import IndexField from './fields/SeoMeta/IndexField';
import DetailField from './fields/SeoMeta/DetailField';
import FormField from './fields/SeoMeta/FormField';

Nova.booting((Vue) => {
  Vue.component('seo-media', SeoMedia);
  Vue.component('index-seo-meta', IndexField);
  Vue.component('detail-seo-meta', DetailField);
  Vue.component('form-seo-meta', FormField);
});

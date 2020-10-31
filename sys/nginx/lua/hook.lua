local regex = [[/(circleci|circleci/.+|circleci/.+\.svg|circleci\.svg|composerlock|composerlock\.svg|d/daily|d/daily\.png|d/daily\.svg|d/monthly|d/monthly\.png|d/monthly\.svg|d/total|d/total\.png|d/total\.svg|dependents|dependents\.svg|downloads|downloads\.png|downloads\.svg|gitattributes|gitattributes\.svg|license|license\.png|license\.svg|suggesters|suggesters\.svg|v/stable|v/stable\.png|v/stable\.svg|v/unstable|v/unstable\.png|v/unstable\.svg|version|version\.png|version\.svg)$]]
local m = ngx.re.match(ngx.var.request_uri, regex)
if not m then return end

local res = ngx.location.capture("/stats")

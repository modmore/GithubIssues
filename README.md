GithubIssues
============

Reads issues from Github, marked with a specific tag. Development by @rtripault

Usage of the snippet
--------------------

```
<h3>Known Issues</h3>
<ul>
[[!getIssues?
    &tpl=`issue`
    &emptyMsg=`<li>Currently no open issues</li>`
    &label=`Public Issues`
    &state=`open`
    &fromUser=`modmore`
    &forRepo=`GithubIssues`
]]
</ul>

<h3>Closed Issues</h3>
<ul>
[[!getIssues?
    &tpl=`issue`
    &emptyMsg=`<li>No closed issues</li>`
    &label=`Public Issues`
    &state=`closed`
    &fromUser=`modmore`
    &forRepo=`GithubIssues`
]]
</ul>
```

The following array of values is returned by the snippet and array-keys are 
set as MODX placeholders:

```
(
    [url] => https://api.github.com/repos/{github_user}/{repo_name}/issues/{issue_number}
    [labels_url] => https://api.github.com/repos/{github_user}/{repo_name}/issues/{issue_number}/labels{/name}
    [comments_url] => https://api.github.com/repos/{github_user}/{repo_name}/issues/{issue_number}/comments
    [events_url] => https://api.github.com/repos/{github_user}/{repo_name}/issues/{issue_number}/events
    [html_url] => https://github.com/{github_user}/{repo_name}/issues/{issue_number}
    [id] => {issue_id}
    [number] => {issue_number}
    [title] => {issue_title}
    [user] => Array
        (
            [login] => {github_user}
            [id] => {id_of_github_user}
            [avatar_url] => {url_to_you_gravatar}
            [gravatar_id] => {id_of_your_gravatar}
            [url] => https://api.github.com/users/{github_user}
            [html_url] => https://github.com/{github_user}
            [followers_url] => https://api.github.com/users/{github_user}/followers
            [following_url] => https://api.github.com/users/{github_user}/following{/other_user}
            [gists_url] => https://api.github.com/users/{github_user}/gists{/gist_id}
            [starred_url] => https://api.github.com/users/{github_user}/starred{/owner}{/repo}
            [subscriptions_url] => https://api.github.com/users/{github_user}/subscriptions
            [organizations_url] => https://api.github.com/users/{github_user}/orgs
            [repos_url] => https://api.github.com/users/{github_user}/repos
            [events_url] => https://api.github.com/users/{github_user}/events{/privacy}
            [received_events_url] => https://api.github.com/users/{github_user}/received_events
            [type] => User
            [site_admin] => 
        )

    [labels] => Array
        (
            [0] => Array
                (
                    [url] => https://api.github.com/repos/{github_user}/{repo_name}/labels/enhancement
                    [name] => enhancement
                    [color] => 84b6eb
                )

            [1] => Array
                (
                    [url] => https://api.github.com/repos/{github_user}/{repo_name}/labels/public
                    [name] => public
                    [color] => 0052cc
                )

        )

    [state] => open
    [assignee] => Array
        (
            [login] => {github_user}
            [id] => {id_of_github_user}
            [avatar_url] => {url_to_assignee_gravatar}
            [gravatar_id] => {id_of_assigne_gravatar}
            [url] => https://api.github.com/users/{github_user}
            [html_url] => https://github.com/{github_user}
            [followers_url] => https://api.github.com/users/{github_user}/followers
            [following_url] => https://api.github.com/users/{github_user}/following{/other_user}
            [gists_url] => https://api.github.com/users/{github_user}/gists{/gist_id}
            [starred_url] => https://api.github.com/users/{github_user}/starred{/owner}{/repo}
            [subscriptions_url] => https://api.github.com/users/{github_user}/subscriptions
            [organizations_url] => https://api.github.com/users/{github_user}/orgs
            [repos_url] => https://api.github.com/users/{github_user}/repos
            [events_url] => https://api.github.com/users/{github_user}/events{/privacy}
            [received_events_url] => https://api.github.com/users/{github_user}/received_events
            [type] => User
            [site_admin] => 
        )

    [milestone] => 
    [comments] => 0
    [created_at] => {creation_date}
    [updated_at] => {modification_date}
    [closed_at] => 
    [pull_request] => Array
        (
            [html_url] => 
            [diff_url] => 
            [patch_url] => 
        )

    [body] => {the_body_text_of_the_issue}
)
```

In cases where a placeholder value from a multi-dimensional array is used, the placeholder 
syntax changes to use a dot for each node in the array, e.g. [[+assignee.login]] and [[+labels.1.name]]

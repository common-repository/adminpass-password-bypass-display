const { __ } = wp.i18n;
const { registerPlugin } = wp.plugins;
const { PluginDocumentSettingPanel } = wp.editPost;
const { withSelect } = wp.data;
const { Fragment } = wp.element;

const PasswordForAdminPanel = withSelect((select) => {
    const { getEditedPostAttribute } = select("core/editor");
    return {
        password: getEditedPostAttribute("password"),
    };
})(({ password }) => {
    return (
        <Fragment>
            <PluginDocumentSettingPanel
                name="admin-password-panel"
                title={__("Password for Protection")}
                className="admin-password-panel"
            >
                {password ? (
                    <p>
                        {__("Password: ")}
                        <strong>{password}</strong>
                    </p>
                ) : (
                    <p>{__("This post is not password-protected.")}</p>
                )}
            </PluginDocumentSettingPanel>
        </Fragment>
    );
});

registerPlugin("adminpass", {
    render: PasswordForAdminPanel,
});

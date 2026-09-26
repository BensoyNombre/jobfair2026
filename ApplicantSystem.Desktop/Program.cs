using Microsoft.Web.WebView2.Core;
using Microsoft.Web.WebView2.WinForms;
using System.Diagnostics;
using System.Net;
using System.Net.Http;

namespace ApplicantSystem.Desktop;

internal static class Program
{
    private const string LocalAppUrl = "http://localhost/applicant_system/index.php";

    [STAThread]
    private static void Main(string[] args)
    {
        ApplicationConfiguration.Initialize();
        var appUrl = LocalAppUrl;
        if (args.Length > 0
            && Uri.TryCreate(args[0], UriKind.Absolute, out var suppliedUrl)
            && (suppliedUrl.Scheme == Uri.UriSchemeHttp || suppliedUrl.Scheme == Uri.UriSchemeHttps))
        {
            appUrl = suppliedUrl.ToString();
        }

        Application.Run(new ApplicantSystemForm(appUrl));
    }

    private sealed class ApplicantSystemForm : Form
    {
        private readonly WebView2 browser = new();
        private readonly Label statusLabel = new();
        private readonly TextBox serverAddress = new();
        private string appUrl;

        public ApplicantSystemForm(string appUrl)
        {
            this.appUrl = appUrl;
            Text = "Applicant System";
            StartPosition = FormStartPosition.CenterScreen;
            WindowState = FormWindowState.Maximized;
            MinimumSize = new Size(900, 600);
            BackColor = Color.White;

            browser.Dock = DockStyle.Fill;
            browser.CreationProperties = new CoreWebView2CreationProperties
            {
                UserDataFolder = Path.Combine(
                    Environment.GetFolderPath(Environment.SpecialFolder.LocalApplicationData),
                    "ApplicantSystem",
                    "WebView2")
            };

            statusLabel.Dock = DockStyle.Fill;
            statusLabel.TextAlign = ContentAlignment.MiddleCenter;
            statusLabel.Font = new Font("Segoe UI", 12F);
            statusLabel.Text = "Checking the server...";

            Controls.Add(statusLabel);
            Shown += async (_, _) => await StartBrowserAsync();
        }

        private async Task StartBrowserAsync()
        {
            if (!await IsAppAvailableAsync())
            {
                ShowServerError();
                return;
            }

            try
            {
                await browser.EnsureCoreWebView2Async();
                browser.CoreWebView2.Settings.AreDefaultContextMenusEnabled = true;
                browser.CoreWebView2.Settings.AreDevToolsEnabled = false;
                browser.CoreWebView2.NavigationCompleted += (_, args) =>
                {
                    if (!args.IsSuccess)
                    {
                        ShowServerError();
                    }
                };

                Controls.Clear();
                Controls.Add(browser);
                browser.Source = new Uri(appUrl);
            }
            catch (Exception exception)
            {
                ShowError("The desktop engine could not start.", exception.Message);
            }
        }

        private async Task<bool> IsAppAvailableAsync()
        {
            try
            {
                using var client = new HttpClient { Timeout = TimeSpan.FromSeconds(3) };
                using var response = await client.GetAsync(appUrl);
                return response.StatusCode == HttpStatusCode.OK;
            }
            catch (HttpRequestException)
            {
                return false;
            }
            catch (TaskCanceledException)
            {
                return false;
            }
        }

        private void ShowServerError()
        {
            Controls.Clear();

            var panel = new TableLayoutPanel
            {
                Dock = DockStyle.Fill,
                ColumnCount = 1,
                RowCount = 5,
                Padding = new Padding(40),
            };
            panel.RowStyles.Add(new RowStyle(SizeType.Percent, 35));
            panel.RowStyles.Add(new RowStyle(SizeType.AutoSize));
            panel.RowStyles.Add(new RowStyle(SizeType.AutoSize));
            panel.RowStyles.Add(new RowStyle(SizeType.AutoSize));
            panel.RowStyles.Add(new RowStyle(SizeType.Percent, 65));

            var title = new Label
            {
                Text = "Applicant System is not running",
                AutoSize = true,
                Anchor = AnchorStyles.Bottom,
                Font = new Font("Segoe UI", 22F, FontStyle.Bold),
            };
            var message = new Label
            {
                Text = "Make sure Desktop A is on the same network with Apache and MySQL running. Enter its IPv4 address below.",
                AutoSize = true,
                Anchor = AnchorStyles.Top,
                Font = new Font("Segoe UI", 12F),
            };
            serverAddress.PlaceholderText = "For example, 192.168.1.25";
            serverAddress.Dock = DockStyle.Top;
            serverAddress.Margin = new Padding(0, 12, 0, 12);
            var retry = new Button
            {
                Text = "Connect",
                AutoSize = true,
                Anchor = AnchorStyles.Top,
                Padding = new Padding(18, 8, 18, 8),
            };
            retry.Click += async (_, _) =>
            {
                if (!TryGetAppUrl(serverAddress.Text, out var targetUrl))
                {
                    MessageBox.Show(
                        "Enter Desktop A's IPv4 address or computer name.",
                        "Applicant System",
                        MessageBoxButtons.OK,
                        MessageBoxIcon.Information);
                    return;
                }

                appUrl = targetUrl;
                Controls.Clear();
                Controls.Add(statusLabel);
                statusLabel.Text = "Connecting to the server...";
                await StartBrowserAsync();
            };

            panel.Controls.Add(title, 0, 0);
            panel.Controls.Add(message, 0, 1);
            panel.Controls.Add(serverAddress, 0, 2);
            panel.Controls.Add(retry, 0, 3);
            Controls.Add(panel);
        }

        private static bool TryGetAppUrl(string address, out string targetUrl)
        {
            targetUrl = LocalAppUrl;
            address = address.Trim();
            if (address.Length == 0)
            {
                return true;
            }

            if (address.StartsWith("http://", StringComparison.OrdinalIgnoreCase)
                || address.StartsWith("https://", StringComparison.OrdinalIgnoreCase))
            {
                if (!Uri.TryCreate(address, UriKind.Absolute, out var suppliedUrl)
                    || (suppliedUrl.Scheme != Uri.UriSchemeHttp && suppliedUrl.Scheme != Uri.UriSchemeHttps))
                {
                    return false;
                }

                targetUrl = suppliedUrl.ToString();
                return true;
            }

            if (address.Contains('/') || address.Contains('\\') || address.Contains('@')
                || !Uri.TryCreate($"http://{address}/applicant_system/index.php", UriKind.Absolute, out var appUri)
                || string.IsNullOrWhiteSpace(appUri.Host))
            {
                return false;
            }

            targetUrl = appUri.ToString();
            return true;
        }

        private void ShowError(string title, string detail)
        {
            MessageBox.Show(
                $"{title}\n\n{detail}",
                "Applicant System",
                MessageBoxButtons.OK,
                MessageBoxIcon.Error);
            Close();
        }
    }
}

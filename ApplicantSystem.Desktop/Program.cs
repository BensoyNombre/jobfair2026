using Microsoft.Web.WebView2.Core;
using Microsoft.Web.WebView2.WinForms;
using System.Diagnostics;
using System.Net;
using System.Net.Http;

namespace ApplicantSystem.Desktop;

internal static class Program
{
    private const string AppUrl = "http://localhost/applicant_system/index.php";

    [STAThread]
    private static void Main()
    {
        ApplicationConfiguration.Initialize();
        Application.Run(new ApplicantSystemForm());
    }

    private sealed class ApplicantSystemForm : Form
    {
        private readonly WebView2 browser = new();
        private readonly Label statusLabel = new();

        public ApplicantSystemForm()
        {
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
            statusLabel.Text = "Checking the local PHP server...";

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
                browser.Source = new Uri(AppUrl);
            }
            catch (Exception exception)
            {
                ShowError("The desktop engine could not start.", exception.Message);
            }
        }

        private static async Task<bool> IsAppAvailableAsync()
        {
            try
            {
                using var client = new HttpClient { Timeout = TimeSpan.FromSeconds(3) };
                using var response = await client.GetAsync(AppUrl);
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
                RowCount = 4,
                Padding = new Padding(40),
            };
            panel.RowStyles.Add(new RowStyle(SizeType.Percent, 35));
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
                Text = "Start Apache and MySQL in XAMPP, then select Retry.",
                AutoSize = true,
                Anchor = AnchorStyles.Top,
                Font = new Font("Segoe UI", 12F),
            };
            var retry = new Button
            {
                Text = "Retry",
                AutoSize = true,
                Anchor = AnchorStyles.Top,
                Padding = new Padding(18, 8, 18, 8),
            };
            retry.Click += async (_, _) =>
            {
                Controls.Clear();
                Controls.Add(statusLabel);
                statusLabel.Text = "Checking the local PHP server...";
                await StartBrowserAsync();
            };

            panel.Controls.Add(title, 0, 0);
            panel.Controls.Add(message, 0, 1);
            panel.Controls.Add(retry, 0, 2);
            Controls.Add(panel);
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
